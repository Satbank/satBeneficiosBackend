<?php

use App\Http\Controllers\CartaoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ComercioController;
use App\Http\Controllers\PrefeituraController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:api'])->group(function () {
    Route::apiResource('/user', UsuarioController::class);
    Route::apiResource('/comercio', ComercioController::class);
    Route::apiResource('/prefeitura', PrefeituraController::class);
    Route::apiResource('/cliente', ClienteController::class);
    Route::apiResource('/cartao', CartaoController::class);

    //Route::apiResource('/empresas', ComercioController::class);
});