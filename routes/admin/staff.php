<?php

use App\Http\Controllers\StaffController;
use Illuminate\Support\Facades\Route;

Route::get('/staff/lists', [StaffController::class, 'lists']);
Route::post('/staff/create', [StaffController::class, 'create']);
Route::post('/staff/update', [StaffController::class, 'update']);
Route::post('/staff/delete', [StaffController::class, 'delete']);


