<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::orderBy('created_at','DESC')->get()->makeVisible('image_src');
        return response()->json($categories);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name'        => 'required|max:20|unique:categories',
            'description' => 'required|max:100',
            'image_file'   => 'required|max:15360',
        ]);

        if($request->hasFile('image_file')) {
            $image = $request->file('image_file');
            $fileName = Str::random(15).'.'.$image->getClientOriginalExtension();
            $uploadDirectory = public_path('files'.DS.'categories');
            $image->move($uploadDirectory, $fileName);
        } 

        $category = Category::create([
            'name'         =>  $request->name,
            'description'  =>  $request->description,
            'image'         =>  $fileName
        ]);
        
        return response()->json('Successfully Added');
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
            'image_file'   => 'required|max:15360',
        ]);

        $category = Category::findOrFail($request->id);
        $fileName = $category->image;
        if($request->hasFile('image_file')) {

            $file = public_path('files'.DS.'categories'.DS.$fileName);

            if(file_exists($file)){
                \File::delete($file);
            }

            $image = $request->file('image_file');
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
