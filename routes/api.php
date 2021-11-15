<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\sellerController; 
use App\Http\Controllers\API\buyerController; 
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
     'middleware' => 'auth.seller'

], function ($router) {
    Route::post('/search-businesses', [sellerController::class, 'searchBusinesses']);  
    Route::post('/make-exchange', [sellerController::class, 'exchange']);  
    Route::post('/get-profile', [sellerController::class, 'getProfile']);  
    Route::get('/scrap', [sellerController::class, 'scrap']);
    Route::get('/draw-chart', [sellerController::class, 'getAvgRates']);
    Route::post('/send-notification', [sellerController::class, 'sendNotification']);
    Route::get('/get-notifications', [sellerController::class, 'getNotifications']);
    Route::get('/get-buyers', [sellerController::class, 'getBuyers']);
    Route::get('/get-Sellernotifications', [sellerController::class, 'getSellerNotifications']);
    Route::post('/delete-notification', [sellerController::class, 'deleteNotification']);
    Route::post('/get-token', [sellerController::class, 'getToken']);
    Route::post('/filter', [sellerController::class, 'filter']);
});

Route::group([
    'middleware' => 'api',
    'middleware' => 'auth.buyer'

], function ($router) {
    Route::post('/add-picture', [buyerController::class, 'addPicture']);  
    Route::post('/send-notification', [buyerController::class, 'sendNotification']);
    Route::get('/get-notifications', [buyerController::class, 'getNotifications']);
    Route::post('/delete-notification', [buyerController::class, 'deleteNotification']);
    Route::post('/get-token', [buyerController::class, 'getToken']);
    Route::get('/remaining-allowance', [buyerController::class, 'remainingAllowance']);
    Route::post('/get-Returndate', [buyerController::class, 'returnDate']);
    Route::get('/daily-sums', [buyerController::class, 'dailySums']);
    Route::get('/return-date', [buyerController::class, 'returnDate']);
    Route::get('/get-Userprofile', [buyerController::class, 'getUserprofile']);
});