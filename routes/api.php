<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Auth\SellerController; 
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::get('/test', [SellerController::class, 'test']);

Route::group([
    'middleware' => 'api',

], function ($router) {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/user-profile', [AuthController::class, 'userProfile']);  
});

Route::group([
     'middleware' => 'api',
    // 'middleware' => 'auth.seller'

], function ($router) {
    Route::post('/add-picture', [AuthController::class, 'addPicture']);  
    Route::post('/search-businesses', [AuthController::class, 'searchBusinesses']);  
    Route::post('/make-exchange', [AuthController::class, 'exchange']);  
    Route::post('/get-profile', [AuthController::class, 'getProfile']);  
    Route::post('/search-category', [AuthController::class, 'searchByCat']);  
    Route::get('/get-chats', [AuthController::class, 'getChatsApi']);  
    Route::get('/get-businesses', [AuthController::class, 'getBusinesses']);   
    Route::get('/scrap', [AuthController::class, 'scrap']);
    Route::get('/draw-chart', [AuthController::class, 'getAvgRates']);
    Route::post('/send-notification', [AuthController::class, 'sendNotification']);
    Route::get('/get-notifications', [AuthController::class, 'getNotifications']);
    Route::get('/get-Sellernotifications', [AuthController::class, 'getSellerNotifications']);
    Route::post('/delete-notification', [AuthController::class, 'deleteNotification']);
    Route::post('/get-token', [AuthController::class, 'getToken']);

});

Route::group([
    'middleware' => 'api',
    'middleware' => 'auth.business'

], function ($router) {
    Route::get('/get-chats', [AuthController::class, 'getChatsApi']);  
    Route::post('/edit',[AuthController::class, 'editApi']);
});