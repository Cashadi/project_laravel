<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
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

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register'])->name('register');

Route::middleware(['auth:api'])->group (
    function () {
        // users
        Route::post('/mlm', [AuthController::class,'addSeller'])->name('users.addSeller');

        // category
        Route::post('/categories', [CategoryController::class,'store'])->name('category.store');
        Route::get('/categories',  [CategoryController::class,'index'])->name('category.index');
        Route::put('/category/{id}', [CategoryController::class,'update'])->name('category.update');
        Route::delete('/category/{id}', [CategoryController::class,'destroy'])->name('category.destroy');

        // products
        Route::post('/products', [ProductController::class,'store'])->name('products.store');
        Route::get('/products', [ProductController::class,'index'])->name('product.index');
        Route::put('/product/{id}', [ProductController::class,'update'])->name('product.update');
        Route::delete('/product/{id}', [ProductController::class,'destroy'])->name('product.destroy');

        // carts
        Route::post('/carts', [CartController::class,'store'])->name('carts.store');
        Route::get('/carts', [CartController::class,'index'])->name('carts.index');
    }
);
