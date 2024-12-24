<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;

Route::post('/user', [UserController::class, 'createUser']);
Route::get('/users', [UserController::class, 'getUsers']);


Route::post('/order', [OrderController::class, 'createOrder']);
Route::get('/orders', [OrderController::class, 'getOrders']);
