<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FamiliaController;
use App\Http\Controllers\ArticuloController;

Route::get('familias', [FamiliaController::class, 'index']);

Route::get('articulos', [ArticuloController::class, 'getProductsWithCustomizations']);