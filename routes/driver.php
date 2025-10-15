<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\SliderController;
use App\Http\Controllers\Dashboard\ProductController;
use App\Http\Controllers\Driver\DriverController;
use App\Http\Controllers\Driver\ProfileController;
use App\Http\Controllers\HomeController;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('login', [AuthController::class, 'login']);
Route::post('after-login', [AuthController::class, 'after_login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('profile', ProfileController::class)->only('index', 'store');
});
