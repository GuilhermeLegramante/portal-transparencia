<?php

use App\Http\Controllers\DespesaController;
use App\Http\Controllers\PlanejamentoController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('home');
});

/**
 * Rotas para o planejamento
 */
Route::get('/planejamento/loa/despesa/{filtro}', [PlanejamentoController::class, 'resumoDespesa'])
    ->name('planejamento.loa.despesa');

Route::get('/planejamento/loa/despesa/elemento/{exercicio}', [PlanejamentoController::class, 'despesaPorElementoDetalhePorExercicio'])
    ->name('planejamento.loa.despesa.elemento.detalhe');

Route::get('/planejamento/loa/despesa/orgao/{exercicio}', [PlanejamentoController::class, 'despesaPorOrgaoDetalhePorExercicio'])
    ->name('planejamento.loa.despesa.orgao.detalhe');

Route::get('/planejamento/loa/despesa/recurso/{exercicio}', [PlanejamentoController::class, 'despesaPorRecursoDetalhePorExercicio'])
    ->name('planejamento.loa.despesa.recurso.detalhe');

Route::get('/planejamento/loa/receita/{filtro}', [PlanejamentoController::class, 'resumoReceita'])
    ->name('planejamento.loa.receita');

Route::get('/planejamento/loa/receita/elemento/{exercicio}', [PlanejamentoController::class, 'receitaPorElementoDetalhePorExercicio'])
    ->name('planejamento.loa.receita.elemento.detalhe');

Route::get('/planejamento/loa/receita/recurso/{exercicio}', [PlanejamentoController::class, 'receitaPorRecursoDetalhePorExercicio'])
    ->name('planejamento.loa.receita.recurso.detalhe');

/**
 * Rotas para as despesas
 */
Route::get('/despesa/diarias/resumo', [DespesaController::class, 'resumoAnualDiarias'])
    ->name('despesa.diarias.resumo');

Route::get('/despesa/diarias/{exercicio}', [DespesaController::class, 'detalheDiarias'])
    ->name('despesa.diarias.detalhe');

Route::get('/despesa/diarias/{exc}/credor/{cad}', [DespesaController::class, 'detalheCredor'])
    ->name('despesa.diarias.credor');

Route::get('/despesa/diarias/{exc}/credor/{cad}/empenho/{emp}', [DespesaController::class, 'detalheEmpenho'])
    ->name('despesa.diarias.empenho');
