<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

include 'admin/branch.php';
include 'admin/category.php';
include 'admin/position.php';
include 'admin/staff.php';
include 'admin/product.php';
include 'admin/invoice.php';
include 'admin/invoice_item.php';
