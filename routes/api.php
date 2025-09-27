<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OrderController;

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

// Этот роут уже есть в Laravel по умолчанию, он просто возвращает
// залогиненного пользователя. Он нам не мешает.
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


// --- НАШИ API РОУТЫ (без авторизации) ---

// GET /api/customers?phone=... — поиск клиента по номеру телефона.
Route::get('/customers', [OrderController::class, 'searchCustomer']);

// GET /api/orders?date=...&status=...&search=... — список заказов с фильтрацией.
Route::get('/orders', [OrderController::class, 'index']);

// POST /api/orders — создание нового заказа.
Route::post('/orders', [OrderController::class, 'store']);

// GET /api/orders/stats — получение статистики заказов.
Route::get('/orders/stats', [OrderController::class, 'stats']);