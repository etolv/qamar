<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\SliderController;
use App\Http\Controllers\Dashboard\ProductController;
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

Route::post('login', [AuthController::class, 'login_api'])->name('login');
Route::post('register', [AuthController::class, 'register'])->name('register');
Route::get('sliders', [SliderController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('/products', ProductController::class);
});

Route::post("test", function () {
    return Category::find(12)->name;
});
