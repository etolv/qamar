<?php

use App\Http\Controllers\API\AddressController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\SliderController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\ServiceController;
use App\Http\Controllers\API\BookingController;
use App\Http\Controllers\API\BranchController;
use App\Http\Controllers\API\BrandController;
use App\Http\Controllers\API\CityController;
use App\Http\Controllers\API\MunicipalController;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\PackageController;
use App\Http\Controllers\API\ProfileController;
use App\Http\Controllers\API\SettingController;
use App\Http\Controllers\API\StockController;
use App\Http\Controllers\API\TripController;
use App\Http\Controllers\Driver\DriverController;
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

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::post('resend', [AuthController::class, 'resend']);
Route::post('verify', [AuthController::class, 'verify']);

Route::post('reset-password', [AuthController::class, 'reset_password']);

Route::get('setting/{key}', [SettingController::class, 'show']);
Route::get('slider', [SliderController::class, 'index']);
Route::apiResource('/category', CategoryController::class)->only('index', 'show');
Route::apiResource('/brand', BrandController::class)->only('index', 'show');
Route::apiResource('/branch', BranchController::class)->only('index', 'show');
Route::apiResource('/product', ProductController::class)->only('index', 'show');
Route::apiResource('/stock', StockController::class)->only('index', 'show');
Route::apiResource('/service', ServiceController::class)->only('index', 'show');
Route::apiResource('/package', PackageController::class)->only('index', 'show');
Route::apiResource('/city', CityController::class)->only('index', 'show');
Route::apiResource('/municipal', MunicipalController::class)->only('index', 'show');

Route::middleware('auth:sanctum')->group(function () {
    Route::resource('notification', NotificationController::class)->only('index');
    Route::middleware('userable:customer', 'verified')->group(function () {
        Route::resource('profile', ProfileController::class)->only('index', 'store');
        Route::resource('order', OrderController::class)->only('index', 'show', 'store');
        Route::resource('booking', BookingController::class);
        Route::resource('address', AddressController::class);
    });
    Route::prefix('employee')->middleware('userable:employee')->group(function () {
        Route::resource('booking', BookingController::class)->only('index', 'show', 'update');
    });
    Route::prefix('driver')->middleware('userable:driver')->group(function () {
        Route::post('update-location', [DriverController::class, 'update_location']);
        Route::resource('trip', TripController::class)->only('index', 'show', 'update');
    });
});
