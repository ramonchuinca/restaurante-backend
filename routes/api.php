<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\OrderController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// 🔓 ROTAS PÚBLICAS
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// 👉 MENU (listar produtos SEM login)
Route::get('/products', [ProductController::class, 'index']);

Route::get('/menu', [CategoryController::class, 'menu']);
// 🔐 ROTAS PROTEGIDAS
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Categorias (admin futuramente)
    Route::apiResource('categories', CategoryController::class);

    // Produtos (admin)
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{product}', [ProductController::class, 'update']);
    Route::delete('/products/{product}', [ProductController::class, 'destroy']);

    // Pedidos (usuário logado)
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::put('/orders/{id}', [OrderController::class, 'update']);
    Route::put('/orders/{id}/items', [OrderController::class, 'updateItems']);
    Route::delete('/orders/{id}', [OrderController::class, 'destroy']);
});
