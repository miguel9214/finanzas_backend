<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TransactionController;

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

Route::get('transactions', [TransactionController::class, 'index']); // Listar transacciones
Route::post('transactions', [TransactionController::class, 'store']); // Crear transacción
Route::get('transactions/{id}', [TransactionController::class, 'show']); // Ver transacción
Route::put('transactions/{id}', [TransactionController::class, 'update']); // Actualizar transacción
Route::delete('transactions/{id}', [TransactionController::class, 'destroy']); // Eliminar transacción



Route::apiResource('categories', CategoryController::class);





