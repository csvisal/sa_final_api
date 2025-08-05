<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/product/lists', [ProductController::class, 'lists']);
Route::post('/product/create', [ProductController::class, 'create']);
Route::post('/product/update', [ProductController::class, 'update']);
Route::post('/product/delete', [ProductController::class, 'delete']);
