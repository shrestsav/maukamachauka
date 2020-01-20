<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AppDefault extends Model
{
    protected $fillable = [
		'OTP_expiry',
		'contact',
		'company_email',
		'company_logo',
		'TACS',
		'FAQS',
		'app_rows',
		'sys_rows',
    ];
    
    protected $casts = [
        'TACS' 		   => 'array',
        'FAQS' 		   => 'array',
    ];
}
