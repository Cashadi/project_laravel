<?php

use App\Http\Controllers\AuthController;
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

        // products
        Route::post('/products', [ProductController::class,'store'])->name('products.store');
        Route::get('/product', [ProductController::class,'index'])->name('product.index');
        Route::put('/product/{id}', [ProductController::class,'update'])->name('product.update');
        Route::delete('/product/{id}', [ProductController::class,'destroy'])->name('product.destroy');
    }
);
