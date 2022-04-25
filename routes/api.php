<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProductController;
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

// User routes
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/profile', [AuthController::class, 'profile'])->middleware('auth:api');
Route::post('/change-password', [AuthController::class, 'changePassword'])->middleware('auth:api');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api');

// Customer CRUD route
Route::apiResource('/customers', CustomerController::class)->middleware('auth:api');

// Category CRUD route
Route::apiResource('/categories', CategoryController::class)->middleware('auth:api');

// Product CRUD route
Route::apiResource('/products', ProductController::class)->middleware('auth:api');

// Invoices CRUD route
Route::apiResource('/invoices', InvoiceController::class)->middleware('auth:api');

// Add invoice products route
Route::post('/invoices/{id}/products', [InvoiceController::class, 'addProducts'])->middleware('auth:api');

// Remove invoice products route
Route::delete('/invoices/{id}/products', [InvoiceController::class, 'removeProducts'])->middleware('auth:api');