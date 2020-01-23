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
	
});

Route::get('/', 'HomeController@index')->name('dashboard');
Route::get('/firebase', 'TestController@firebaseIDToken');

Route::get('/verify-email/{encryptedEmail}','UserController@verifyEmail'); //Verify Email for Mobile App Signups
Route::get('/request-verification/{userID}','UserController@resendVerifyEmail');

Auth::routes();

Route::middleware(['auth'])->group(function () {

	Route::get('/v/{any}', 'HomeController@index')->where('any', '.*');
	Route::group(['prefix' => 'admin', 'middleware' => ['role:superAdmin']], function() {
	    Route::resource('roles','RoleController');
	    Route::resource('users','UserController');
	});
	Route::apiResource('/categories','CategoryController');
	Route::get('/notifications','UserController@notifications');
	Route::get('/markAsRead/{notificationId}','UserController@markAsRead');
	Route::get('/markAllAsRead','UserController@markAllAsRead');

});