<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FamiliaController;
use App\Http\Controllers\ArticuloController;
use App\Http\Controllers\PrintController;

Route::get('familias', [FamiliaController::class, 'index']);

Route::get('articulos', [ArticuloController::class, 'getProductsWithCustomizations']);

Route::get('print-receipt', [PrintController::class, 'printReceipt']);

Route::get('print-receipt-text', [PrintController::class, 'printReceiptPlainText']);
