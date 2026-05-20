<?php

use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\StripeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
 Route::post('/stripe/webhook', [StripeController::class, 'webhook']);
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);

Route::middleware('auth:api')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::put('/profile', [ProfileController::class, 'updateProfile']);
    Route::post('/cart', [CartController::class, 'store']);
    Route::get('/cart', [CartController::class, 'index']);
    Route::delete('/cart/{id}', [CartController::class, 'destroy']);
    Route::get('/cart/count', [CartController::class, 'count']);

    Route::post('/stripe/create-checkout-session', [StripeController::class, 'createCheckoutSession']);
    Route::middleware('role:admin')->group(function () {

        Route::get('/admin/users', [AdminController::class, 'users']);

    });
});
