<?php

use App\Http\Controllers\CartaoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ComercioController;
use App\Http\Controllers\MovimentacaoClienteComercioController;
use App\Http\Controllers\MovimentacaoPrefeituraClienteController;
use App\Http\Controllers\MovimentacaoPrefeituraController;
use App\Http\Controllers\PrefeituraController;
use App\Http\Controllers\RecebimentosSatBankController;
use App\Http\Controllers\UsuarioController;
use App\Models\Recebimentos_sat_bank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth:api'])->group(function () {
    Route::apiResource('/user', UsuarioController::class);
    Route::apiResource('/comercio', ComercioController::class);
    Route::apiResource('/prefeitura', PrefeituraController::class);
    Route::apiResource('/cliente', ClienteController::class);
    Route::apiResource('/cartao', CartaoController::class);
    //movimentações financeiras feita pelo admin
    Route::apiResource('/movimentacaoPrefeitura', MovimentacaoPrefeituraController::class);  
    Route::apiResource('/movimentacaoPrefeituraCliente', MovimentacaoPrefeituraClienteController::class);  
    Route::post('/movimentacaoPrefeituraCliente/alocar-valor-individual', [MovimentacaoPrefeituraClienteController::class, 'alocarValorIndividual']);
    // buscas da dashboard
    Route::get('/totalcartoes',[RecebimentosSatBankController::class , 'totalCartoes']);
    Route::get('/totalclientesbase',[RecebimentosSatBankController::class , 'totalClienteBase']);
    Route::get('/totalcomerciosbase',[RecebimentosSatBankController::class , 'totalComerciosBase']);
    Route::get('/totalano',[RecebimentosSatBankController::class , 'totalAno']);
    Route::apiResource('/totalmes', RecebimentosSatBankController::class);

    //rotas para comercio 
    Route::apiResource('/movimentacaoClienteComercio', MovimentacaoClienteComercioController::class);  
    Route::get('/relatorioComercio', [MovimentacaoClienteComercioController::class, 'getRelatorios']);
    Route::get('/estorno', [MovimentacaoClienteComercioController::class, 'estornar']);
    //Route::apiResource('/empresas', ComercioController::class);
});