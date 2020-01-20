<?php

namespace App\Traits;

use App\DeviceToken;
use App\Mail\notifyMail;
use App\User;
use Mail;

trait NotificationLogics
{
    /**
    * Send Welcome Email
    */
    public static function notifyNewRegistration($user_id)
    {  
        $user = User::find($user_id);
        
        $mailData = [
            'emailType' => 'new_registration',
            'name'      => $user->full_name,
            'email'     => $user->email,
            'subject'   => "GO-RINSE: Welcome ".$user->full_name,
            'message'   => "Welcome to GO-RINSE..",
        ];
        
        // Notify user in email
        Mail::send(new notifyMail($mailData));
        
        return true;
    }
}
