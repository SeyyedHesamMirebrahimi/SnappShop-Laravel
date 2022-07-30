<?php

use App\Http\Controllers\API\RegisterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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


Route::controller(RegisterController::class)->group(function(){
    Route::post('verify', 'verify');
    Route::post('register', 'register');
    Route::post('login', 'login');
});

Route::middleware('auth:sanctum')->group( function () {
    Route::bind('from', function ($value) {
        return \App\Models\CardNumber::where('cart_number', $value)->first() ?? abort(404);
    });
    Route::post('/send-money' ,[\App\Http\Controllers\Api\MoneyController::class , 'send'] );
    Route::get('/most-recent' ,[\App\Http\Controllers\Api\MoneyController::class , 'most_recent'] );
});
