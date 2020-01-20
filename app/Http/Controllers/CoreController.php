<?php

use App\AppDefault;
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CoreController extends Controller
{
    public function appDefaults()
    {
        $appDefaults = AppDefault::first();
        $appDefaults['company_logo_url'] = asset('files/'.$appDefaults->company_logo);        
        return response()->json($appDefaults);
    }    
    
    public function updateAppDefaults(Request $request)
    {
        $input = [];
        if($request->saveType=='generalSetting'){
            $validatedData = $request->validate([
                'VAT' => 'required|numeric',
                'delivery_charge' => 'required|numeric',
                'urgent_charge' => 'required|numeric',
                'EDT' => 'required|numeric',
                'OTP_expiry' => 'required|numeric',
                'app_rows' => 'required|numeric',
                'sys_rows' => 'required|numeric',
                'referral_grant' => 'required|numeric',
            ]);

            $input = $request->only('VAT', 'delivery_charge','urgent_charge','EDT','OTP_expiry','app_rows','sys_rows','referral_grant');
        }
        if($request->saveType=='supportSetting'){
            $validatedData = $request->validate([
                'logoFile' => 'mimes:jpeg,png,jpg|max:6144',
                'company_logo' => 'required|string|max:255',
                'company_email' => 'required|string|email|max:255',
                'hotline_contact' => 'required|string|max:255',
                'FAQ_link' => 'required|string|max:255',
                'online_chat' => 'required',
            ]);

            $input = $request->only('company_logo', 'company_email', 'hotline_contact', 'FAQ_link', 'online_chat');
            $input['online_chat'] = json_decode($request->online_chat,true);

            if ($request->hasFile('logoFile')) {
                $validator = Validator::make($request->all(), [
                    "logoFile" => 'mimes:jpeg,bmp,png|max:3072',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'status' => '422',
                        'message' => 'Validation Failed',
                        'errors' => $validator->errors(),
                    ], 422);
                }
                $photo = $request->file('logoFile');
                $fileName = 'company_logo.'.$photo->getClientOriginalExtension();
                $uploadDirectory = public_path('files');
                $photo->move($uploadDirectory, $fileName);

                $input['company_logo'] = $fileName;
            }
        }
        if($request->saveType=='orderSetting'){
            $input = $request->only('order_time', 'driver_notes');
        }
        if($request->saveType=='TACS'){
            $input = $request->only('TACS');
        }
        if($request->saveType=='FAQS'){
            $input = $request->only('FAQS');
        }
        if($request->saveType=='OTD'){
            $validatedData = $request->validate([
                'OTD'   => 'required|array',
                'OTD.*' => 'required|string',
            ]);
            $input = $request->only('OTD');
        }
        
        $update = AppDefault::firstOrFail()->update($input);

        return response()->json('Successfully Updated');
    }
}
