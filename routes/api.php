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

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/profile', [AuthController::class, 'profile'])->middleware('auth:api');

Route::apiResource('/customers', CustomerController::class)->middleware('auth:api');

Route::apiResource('/categories', CategoryController::class)->middleware('auth:api');

Route::apiResource('/products', ProductController::class)->middleware('auth:api');

Route::apiResource('/invoices', InvoiceController::class)->middleware('auth:api');

Route::post('/invoices/{id}/products', [InvoiceController::class, 'addProducts'])->middleware('auth:api');
Route::delete('/invoices/{id}/products', [InvoiceController::class, 'removeProducts'])->middleware('auth:api');