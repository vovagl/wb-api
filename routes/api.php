<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\StockController;
use App\Http\Controllers\Api\IncomeController;
use App\Http\Controllers\Api\SalesController;

Route::get('/orders', [OrderController::class, 'index']);
Route::get('/sales', [SalesController::class, 'index']);
Route::get('/stocks', [StockController::class, 'index']);
Route::get('/incomes/sync', [IncomeController::class, 'sync']);
Route::get('/incomes', [IncomeController::class, 'index']);