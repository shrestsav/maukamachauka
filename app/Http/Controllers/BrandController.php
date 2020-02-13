<?php

namespace App\Http\Controllers;

use App\Brand;
use App\Category;
use App\BrandBanner;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $brands = Brand::with('categories')->orderBy('created_at','DESC')->get()->makeVisible('logo_src');
        return response()->json($brands);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        foreach($request->all() as $key => $req){
            if($req=="null"){
                $request[$key] = null;
            }
        }
        $request['categories'] = json_decode($request['categories']);
        $validatedData = $request->validate([
            'name'         => 'required|max:50',
            'description'  => 'required|max:500',
            'logo_file'    => 'required|mimes:jpeg,bmp,png|max:15360',
            'categories'   => 'required|array',
        ]);

        if($request->hasFile('logo_file')) {
            $image = $request->file('logo_file');
            $fileName = Str::uuid().'.'.$image->getClientOriginalExtension();
            $uploadDirectory = public_path('files'.DS.'brands');
            $image->move($uploadDirectory, $fileName);
        } 

        $brand = new Brand();
        $brand->name = $request->name;
        $brand->email = $request->email;
        $brand->url = $request->url;
        $brand->logo = $fileName;
        $brand->cp_name = $request->cp_name;
        $brand->cp_designation = $request->cp_designation;
        $brand->cp_contact = $request->cp_contact;
        $brand->description = $request->description;
        $brand->save();

        //Attaching categories to the brands
        $cat_ids = collect($request['categories'])->pluck('id');
        $brand->categories()->attach($cat_ids);

        //Save Brand Banner Images
        for($i=1; $i<=5; $i++){
            if ($request->hasFile('img'.$i.'_file')) {
                $rand = Str::uuid();
                $image = $request->file('img'.$i.'_file');
                $fileName = $rand.'_large.'.$image->getClientOriginalExtension();
                $this->storeImage($image, $brand->id, $fileName);

                $brandBanner = BrandBanner::create([
                    'brand_id'  =>  $brand->id,
                    'image'     =>  $fileName
                ]);                
            }
        }

        return response()->json([
            'message'  =>  'brand saved successfully'
        ]);
    }

    /**
     * Image Processing with Intervention
     */
    public function storeImage($image, $pID, $fileName)
    {
        $image = Image::make($image);

        // prevent possible upsizing
        $image->resize(null, 800, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        $uploadDirectory = public_path('files'.DS.'brands');
        if (!file_exists($uploadDirectory)) {
            \File::makeDirectory($uploadDirectory, 0755, true);
        }
        $image->save($uploadDirectory.DS.$fileName,80);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'id'          => 'required|numeric',
            'description' => 'required|max:100',
            'logo_file'   => 'required|mimes:jpeg,bmp,png|max:15360',
        ]);

        $category = Category::findOrFail($request->id);
        $fileName = $category->image;
        if($request->hasFile('logo_file')) {

            $file = public_path('files'.DS.'categories'.DS.$fileName);

            if(file_exists($file)){
                \File::delete($file);
            }

            $image = $request->file('logo_file');
            $fileName = Str::random(15).'.'.$image->getClientOriginalExtension();
            $uploadDirectory = public_path('files'.DS.'categories');
            $image->move($uploadDirectory, $fileName);
        }

        
        $category->description = $request->description;
        $category->image = $fileName;
        $category->save();

        return response()->json('Successfully Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category = Category::findOrFail($id)->makeVisible('can_delete');

        if($category['can_delete']){
            $file = public_path('files'.DS.'categories'.DS.$category->image);

            if(file_exists($file)){
                \File::delete($file);
            }
            $category->delete();

            return response()->json([
                'message' => 'Successfully Deleted'
            ]);
        }

        return response()->json([
            'message' => 'Sorry You cannot delete this'
        ], 403);
    }
}
