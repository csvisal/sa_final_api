<?php

use App\Http\Controllers\InvoiceController;
use Illuminate\Support\Facades\Route;

Route::get('/invoice/lists',  [InvoiceController::class, 'lists']);
Route::post('/invoice/create', [InvoiceController::class, 'create']);
Route::post('/invoice/update', [InvoiceController::class, 'update']);
Route::post('/invoice/delete', [InvoiceController::class, 'delete']);
