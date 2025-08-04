<?php

use App\Http\Controllers\BranchController;
use Illuminate\Support\Facades\Route;

Route::get('/branch/lists', [BranchController::class, 'lists']);
Route::post('/branch/create', [BranchController::class, 'create']);
Route::post('/branch/update', [BranchController::class, 'update']);
Route::post('/branch/delete', [BranchController::class, 'delete']);


