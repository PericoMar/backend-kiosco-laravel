<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FamiliaController;
use App\Http\Controllers\ArticuloController;
use App\Http\Controllers\PrintController;
use App\Http\Controllers\PreguntaArticuloController;
use App\Http\Controllers\OpcionPreguntaArticuloController;

Route::get('familias', [FamiliaController::class, 'index']);
Route::post('familia', [FamiliaController::class, 'store']);

Route::get('articulos', [ArticuloController::class, 'getProductsWithCustomizations']);
Route::post('articulo', [ArticuloController::class, 'store']);
Route::put('articulo/{id}', [ArticuloController::class, 'update']);
Route::get('articulo/{productType}/{id}', [ArticuloController::class, 'show']);

Route::get('preguntas', [PreguntaArticuloController::class, 'index']);
Route::post('pregunta', [PreguntaArticuloController::class, 'store']);
Route::put('pregunta/{id}', [PreguntaArticuloController::class, 'update']);

Route::post('opcion', [OpcionPreguntaArticuloController::class, 'store']);
Route::put('opcion/{id}', [OpcionPreguntaArticuloController::class, 'update']);

Route::get('print-receipt', [PrintController::class, 'printReceipt']);

Route::post('print-receipt-text', [PrintController::class, 'printReceiptPlainText']);
