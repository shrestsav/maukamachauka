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

	Route::get('/offer/like/add/{offerID}','OfferController@likeOffer');
	Route::delete('/offer/like/remove/{offerID}','OfferController@removeLikeOffer');

	Route::get('/offer/favorite/add/{offerID}','OfferController@addToFavorites');
	Route::delete('/offer/favorite/remove/{offerID}','OfferController@removeFromFavorites');

	Route::get('/offer/favorites','OfferController@userFavoriteOffers');

	Route::get('/brands/{brandID}','OfferController@brandDetails');
	Route::get('/brand/offers/{brandID}','OfferController@brandOffers');
	Route::get('/category/offers/{catID}','OfferController@categoryOffers');

	Route::get('/categories','TagsController@index');

	Route::get('/user/tag/subscribed','UserController@subscribedTags');

	Route::get('/user/tag/subscribe/{catID}','UserController@subscribeTag');
	Route::delete('/user/tag/unSubscribe/{catID}','UserController@unSubscribeTag');

	Route::get('/checkRole','AuthController@checkRole');

	Route::group(['middleware' => ['role:user']], function() {
		Route::get('/profile','UserController@profile');
		Route::post('/profile/update','UserController@updateProfile');
	});

	Route::get('/tokens','AuthController@tokens');

	Route::get('/notifications','AuthController@notifications');
	Route::get('/countUnreadNotifications','AuthController@countUnreadNotifications');
	Route::get('/markAsRead/{notificationID}','AuthController@markAsRead');
	Route::get('/markAllAsRead','AuthController@markAllAsRead');

	Route::post('/deviceToken/remove','AuthController@removeDeviceToken');
});