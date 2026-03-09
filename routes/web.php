<?php

use App\Http\Controllers\PlanejamentoController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('home');
});

Route::get('/planejamento/loa/despesa/elemento', [PlanejamentoController::class, 'despesaPorElemento'])
    ->name('planejamento.loa.despesa.elemento');

Route::get('/planejamento/loa/despesa/elemento/{exercicio}', [PlanejamentoController::class, 'despesaPorElementoDetalhePorExercicio'])
    ->name('planejamento.loa.despesa.elemento.detalhe');
