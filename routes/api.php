<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FamiliaController;
use App\Http\Controllers\ArticuloController;
use App\Http\Controllers\PrintController;
use App\Http\Controllers\PreguntaArticuloController;
use App\Http\Controllers\OpcionPreguntaArticuloController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\UsuariosController;
use App\Http\Controllers\KioscosController;
use App\Http\Controllers\ClientesController;
use App\Http\Controllers\DatafonoController;
use App\Http\Controllers\ImpresoraController;

Route::post('login', [UsuariosController::class, 'login']);

Route::get('kioskos/{clienteId}', [KioscosController::class, 'index']);
Route::get('kiosko/{numSerie}', [KioscosController::class, 'getKioscoByNumSerie']);
Route::get('estado-suscripcion/{clienteId}/kiosco', [ClientesController::class, 'getSubscriptionStatus']);
Route::post('kiosko', [KioscosController::class, 'store']);
Route::put('kiosko/{id}', [KioscosController::class, 'update']);
Route::delete('kiosko/{id}', [KioscosController::class, 'destroy']);

Route::get('datafonos/{cliente_id}', [DatafonoController::class, 'index']);
Route::post('datafono', [DatafonoController::class, 'store']);
Route::put('datafono/{id}', [DatafonoController::class, 'update']);
Route::delete('datafono/{id}', [DatafonoController::class, 'destroy']);

Route::get('impresoras/{cliente_id}', [ImpresoraController::class, 'index']);
Route::post('impresora', [ImpresoraController::class, 'store']);
Route::delete('impresora/{id}', [ImpresoraController::class, 'destroy']);
Route::put('impresora/{id}', [ImpresoraController::class, 'update']);

Route::get('familias/{cliente_id}', [FamiliaController::class, 'index']);
Route::post('familia/{cliente_id}', [FamiliaController::class, 'store']);
Route::get('familia/{id}', [FamiliaController::class, 'show']);
Route::put('familia/{id}', [FamiliaController::class, 'update']);
Route::delete('familia/{id}', [FamiliaController::class, 'destroy']);

Route::get('articulos/{cliente_id}', [ArticuloController::class, 'getProductsWithCustomizations']);
Route::post('articulo/{cliente_id}', [ArticuloController::class, 'store']);
Route::put('articulo/{id}', [ArticuloController::class, 'update']);
Route::delete('articulo/{id}', [ArticuloController::class, 'destroy']);
Route::get('articulo/{productType}/{id}', [ArticuloController::class, 'show']);

Route::get('preguntas/{cliente_id}', [PreguntaArticuloController::class, 'index']);
Route::post('pregunta/{cliente_id}', [PreguntaArticuloController::class, 'store']);
Route::put('pregunta/{id}', [PreguntaArticuloController::class, 'update']);
Route::delete('pregunta/{id}', [PreguntaArticuloController::class, 'destroy']);

Route::post('opcion/{cliente_id}', [OpcionPreguntaArticuloController::class, 'store']);
Route::put('opcion/{cliente_id}/{id}', [OpcionPreguntaArticuloController::class, 'update']);
Route::delete('opcion/{id}', [OpcionPreguntaArticuloController::class, 'destroy']);

Route::post('/upload-image/{tableName}/{recordId}/{columnName}', [ImageController::class, 'uploadImage']);

Route::post('print-receipt', [PrintController::class, 'printPDF']); 

Route::post('print-receipt-text', [PrintController::class, 'printReceiptPlainText']);

Route::post('payment', [PaymentController::class, 'payment']);
Route::get('payment/status/{terminalSessionId}', [PaymentController::class, 'getTerminalSession']);
Route::put('payment/{terminalSessionId}/cancel', [PaymentController::class, 'cancelPayment']);
Route::put('payment/signature-verification/{terminalSessionId}', [PaymentController::class, 'signatureVerification']);
