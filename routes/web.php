<?php

use App\Events\TaskEvent;
use App\Jobs\PendingNotification;
use App\ReferralGrant;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['prefix' => 'test'], function() {
	Route::get('/', function(){
		// $OTP_timestamp = \Carbon\Carbon::parse($this->OTP_timestamp);
        $current = \Carbon\Carbon::now()->format('Y-m-d H:i:s');
        // $totalTime = \Carbon\Carbon::now()->diffInMinutes($OTP_timestamp);
        return $current;
        // if($this->OTP==$OTP && $totalTime<=config('settings.OTP_expiry'))
        //     return true;

        // return false;
		return Date('Y-m-d H:i:s');
	});
});

Route::get('/', 'HomeController@index')->name('dashboard');
Route::get('/firebase', 'TestController@firebaseIDToken');

Route::get('/verify-email/{encryptedEmail}','UserController@verifyEmail'); //Verify Email for Mobile App Signups
Route::get('/request-verification/{userID}','UserController@resendVerifyEmail');

Auth::routes();

// Temp Route
Route::get('/authUser', function(){
	return response()->json('Success');
});
Route::middleware(['auth'])->group(function () {

	Route::get('/v/{any}', 'HomeController@index')->where('any', '.*');
	Route::group(['prefix' => 'admin', 'middleware' => ['role:superAdmin']], function() {
	    Route::resource('roles','RoleController');
	    Route::resource('users','UserController');
	});
	Route::apiResource('/categories','CategoryController');
	Route::apiResource('/brands','BrandController');
	Route::apiResource('/offers','OfferController');
	Route::get('/notifications','UserController@notifications');
	Route::get('/markAsRead/{notificationId}','UserController@markAsRead');
	Route::get('/markAllAsRead','UserController@markAllAsRead');

});