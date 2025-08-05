<?php

use App\Http\Controllers\InvoiceItemController;
use Illuminate\Support\Facades\Route;

Route::get('/invoice_item/lists', [InvoiceItemController::class, 'lists']);
Route::post('/invoice_item/create', [InvoiceItemController::class, 'create']);
Route::post('/invoice_item/update', [InvoiceItemController::class, 'update']);
Route::post('/invoice_item/delete', [InvoiceItemController::class, 'delete']);
