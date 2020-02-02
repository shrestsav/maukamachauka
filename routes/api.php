<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::post('/register_user','Api\AuthController@register');
Route::post('/login','Api\AuthController@login');
Route::post('/verifyOTP','Api\AuthController@verifyOTP');

Route::post('/socialLogin','Api\AuthController@socialLogin');

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});



Route::group(['namespace' => 'Api', 'middleware' => ['auth:api']], function() {
	Route::get('/offers','OfferController@index');
	Route::get('/offer/like/{offerID}','OfferController@likeOffer');
	Route::get('/offer/removeLike/{offerID}','OfferController@removeLikeOffer');
	Route::get('/brands/{brandID}','OfferController@brandDetails');
	Route::get('/brand/offers/{brandID}','OfferController@brandOffers');
	Route::get('/category/offers/{catID}','OfferController@categoryOffers');
	

	Route::get('/checkRole','AuthController@checkRole');

	Route::group(['middleware' => ['role:user']], function() {
		Route::post('/createProfile','AuthController@createProfile');
	});

	Route::get('/tokens','AuthController@tokens');

	Route::get('/notifications','AuthController@notifications');
	Route::get('/countUnreadNotifications','AuthController@countUnreadNotifications');
	Route::get('/markAsRead/{notificationID}','AuthController@markAsRead');
	Route::get('/markAllAsRead','AuthController@markAllAsRead');

	Route::post('/deviceToken/remove','AuthController@removeDeviceToken');

});