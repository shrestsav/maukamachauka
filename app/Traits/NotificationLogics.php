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
    public static function notifyNewRegistration($user)
    {   
        $verifyLink = url('verify-email/'.encrypt($user->email));
        $mailData = [
            'emailType'  => 'new_registration',
            'name'       => $user->full_name,
            'email'      => $user->email,
            'subject'    => "Welcome ".$user->full_name,
            'message'    => "Welcome to Maukamachauka. Please use the link below to verify your email.",
            'verifyLink' => $verifyLink,
        ];
        
        // Notify user in email
        Mail::send(new notifyMail($mailData));
        
        return true;
    }      

    /**
    * Send Verify Email
    */
    public static function notifyVerifyEmailChange($user)
    {   
        $verifyLink = url('verify-email/'.encrypt($user->email));
        $mailData = [
            'emailType'  => 'new_registration',
            'name'       => $user->full_name,
            'email'      => $user->email,
            'subject'    => "Welcome ".$user->full_name,
            'message'    => "Welcome to Maukamachauka. Please use the link below to verify your email.",
            'verifyLink' => $verifyLink,
        ];
        
        // Notify user in email
        Mail::send(new notifyMail($mailData));
        
        return true;
    }
}
