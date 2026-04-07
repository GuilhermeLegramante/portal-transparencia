<?php

use App\Http\Controllers\ContratoController;
use App\Http\Controllers\DespesaController;
use App\Http\Controllers\EmpenhoCredorController;
use App\Http\Controllers\EmpenhoElementoController;
use App\Http\Controllers\EmpenhoOrgaoController;
use App\Http\Controllers\EmpenhoRecursoController;
use App\Http\Controllers\ExecucaoElementoController;
use App\Http\Controllers\ExecucaoLocalizadorController;
use App\Http\Controllers\ExecucaoOrgaoController;
use App\Http\Controllers\ExecucaoRecursoController;
use App\Http\Controllers\LicitacaoController;
use App\Http\Controllers\PlanejamentoController;
use App\Http\Controllers\QuadroFuncionalController;
use App\Http\Controllers\ReceitaController;
use App\Http\Controllers\RegistroPrecoController;
use App\Http\Controllers\RequisicaoController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('home');
})->name('home');

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

Route::get('/despesa/diarias/{exercicio}/credor/{cad}', [DespesaController::class, 'detalheCredor'])
    ->name('despesa.diarias.credor');

Route::get('/despesa/diarias/{exercicio}/credor/{cad}/empenho/{emp}', [DespesaController::class, 'detalheEmpenho'])
    ->name('despesa.diarias.empenho');


Route::prefix('despesa/empenho-por-credor')->group(function () {
    // 1. Resumo por Exercício
    Route::get('/', [EmpenhoCredorController::class, 'index'])->name('empenho.credor.index');

    // 2. Lista de Credores no Exercício
    Route::get('/{exercicio}', [EmpenhoCredorController::class, 'listaCredores'])->name('empenho.credor.lista');

    // 3. Detalhes (Credor + Lista de Empenhos)
    Route::get('/{exercicio}/{credor_id}', [EmpenhoCredorController::class, 'detalhes'])->name('empenho.credor.detalhes');

    // 4. Detalhes do Empenho específico
    Route::get('/{exercicio}/credor/{credor_id}/empenho/{empenho_id}', [EmpenhoCredorController::class, 'detalheEmpenho'])
        ->name('empenho.credor.empenho.detalhe');
});

Route::prefix('despesa/empenho-por-elemento')->group(function () {
    // 1. Resumo por Exercício
    Route::get('/', [EmpenhoElementoController::class, 'index'])->name('empenho.elemento.index');

    // 2. Lista de Elementos no Exercício
    Route::get('/{exercicio}', [EmpenhoElementoController::class, 'listaElementos'])->name('empenho.elemento.lista');

    // 3. Detalhes (Elemento + Lista de Empenhos)
    Route::get('/{exercicio}/{elemento_id}', [EmpenhoElementoController::class, 'detalhes'])->name('empenho.elemento.detalhes');

    // 4. Detalhes do Empenho específico
    Route::get('/{exercicio}/elemento/{elemento_id}/empenho/{empenho_id}', [EmpenhoElementoController::class, 'detalheEmpenho'])
        ->name('empenho.elemento.empenho.detalhe');
});

Route::prefix('despesa/empenho-por-orgao')->group(function () {
    // 1. Resumo por Exercício
    Route::get('/', [EmpenhoOrgaoController::class, 'index'])->name('empenho.orgao.index');

    // 2. Lista de Órgãos no Exercício
    Route::get('/{exercicio}', [EmpenhoOrgaoController::class, 'listaOrgaos'])->name('empenho.orgao.lista');

    // 3. Detalhes (Órgão + Lista de Empenhos)
    Route::get('/{exercicio}/{orgao_id}', [EmpenhoOrgaoController::class, 'detalhes'])->name('empenho.orgao.detalhes');

    // 4. Detalhes do Empenho específico
    Route::get('/{exercicio}/orgao/{orgao_id}/empenho/{empenho_id}', [EmpenhoOrgaoController::class, 'detalheEmpenho'])
        ->name('empenho.orgao.empenho.detalhe');
});

Route::prefix('despesa/empenho-por-recurso')->group(function () {
    // 1. Resumo por Exercício
    Route::get('/', [EmpenhoRecursoController::class, 'index'])->name('empenho.recurso.index');

    // 2. Lista de Recursos no Exercício
    Route::get('/{exercicio}', [EmpenhoRecursoController::class, 'listaRecursos'])->name('empenho.recurso.lista');

    // 3. Detalhes (Recurso + Lista de Empenhos)
    Route::get('/{exercicio}/{recurso_id}', [EmpenhoRecursoController::class, 'detalhes'])->name('empenho.recurso.detalhes');

    // 4. Detalhes do Empenho específico
    Route::get('/{exercicio}/recurso/{recurso_id}/empenho/{empenho_id}', [EmpenhoRecursoController::class, 'detalheEmpenho'])
        ->name('empenho.recurso.empenho.detalhe');
});


Route::prefix('despesa/execucao-por-elemento')->group(function () {
    // 1. Resumo por Exercício
    Route::get('/', [ExecucaoElementoController::class, 'index'])->name('execucao.elemento.index');

    // 2. Lista de Elementos no Exercício
    Route::get('/{exercicio}', [ExecucaoElementoController::class, 'list'])->name('execucao.elemento.list');
});

Route::prefix('despesa/execucao-por-orgao')->group(function () {
    // 1. Resumo por Exercício
    Route::get('/', [ExecucaoOrgaoController::class, 'index'])->name('execucao.orgao.index');

    // 2. Lista de orgaos no Exercício
    Route::get('/{exercicio}', [ExecucaoOrgaoController::class, 'list'])->name('execucao.orgao.list');
});

Route::prefix('despesa/execucao-por-recurso')->group(function () {
    // 1. Resumo por Exercício
    Route::get('/', [ExecucaoRecursoController::class, 'index'])->name('execucao.recurso.index');

    // 2. Lista de recursos no Exercício
    Route::get('/{exercicio}', [ExecucaoRecursoController::class, 'list'])->name('execucao.recurso.list');
});

Route::prefix('despesa/execucao-por-localizador')->group(function () {
    // 1. Resumo por Exercício
    Route::get('/', [ExecucaoLocalizadorController::class, 'index'])->name('execucao.localizador.index');

    // 2. Lista de recursos no Exercício
    Route::get('/{exercicio}', [ExecucaoLocalizadorController::class, 'list'])->name('execucao.localizador.list');
});

/**
 * Rotas para a Receita
 */
Route::prefix('receita')->name('receita.')->group(function () {

    // ARRECADAÇÃO (3 Níveis)
    Route::prefix('arrecadacao')->name('arrecadacao.')->group(function () {
        // Por Elemento
        Route::get('/elemento', [ReceitaController::class, 'arrecadacaoElementoIndex'])->name('elemento.index');
        Route::get('/elemento/{exercicio}/lista', [ReceitaController::class, 'arrecadacaoElementoList'])->name('elemento.list');
        Route::get('/elemento/{exercicio}/{elemento}/detalhes', [ReceitaController::class, 'arrecadacaoElementoDetails'])->name('elemento.details');

        // Por Recurso
        Route::get('/recurso', [ReceitaController::class, 'arrecadacaoRecursoIndex'])->name('recurso.index');
        Route::get('/recurso/{exercicio}/lista', [ReceitaController::class, 'arrecadacaoRecursoList'])->name('recurso.list');
        Route::get('/recurso/{exercicio}/{recurso}/detalhes', [ReceitaController::class, 'arrecadacaoRecursoDetails'])->name('recurso.details');
    });

    // EXECUÇÃO (2 Níveis)
    Route::prefix('execucao')->name('execucao.')->group(function () {
        // Por Elemento
        Route::get('/elemento', [ReceitaController::class, 'execucaoElementoIndex'])->name('elemento.index');
        Route::get('/elemento/{exercicio}/lista', [ReceitaController::class, 'execucaoElementoList'])->name('elemento.list');

        // Por Recurso
        Route::get('/recurso', [ReceitaController::class, 'execucaoRecursoIndex'])->name('recurso.index');
        Route::get('/recurso/{exercicio}/lista', [ReceitaController::class, 'execucaoRecursoList'])->name('recurso.list');
    });
});

Route::prefix('compras')->name('compras.')->group(function () {
    Route::prefix('licitacoes')->name('licitacoes.')->group(function () {
        // Processo Licitatório
        Route::get('/processo-licitatorio', [LicitacaoController::class, 'index'])->name('processo.index');
        Route::get('/processo-licitatorio/{exercicio}', [LicitacaoController::class, 'list'])->name('processo.list');
        Route::get('/processo-licitatorio/detalhes/{id}', [LicitacaoController::class, 'show'])->name('processo.show');

        // Contratos Administrativos
        Route::get('/contratos', [ContratoController::class, 'index'])->name('contrato.index');
        Route::get('/contratos/lista/{exercicio}', [ContratoController::class, 'list'])->name('contrato.list');
        Route::get('/contratos/detalhes/{id}', [ContratoController::class, 'show'])->name('contrato.show');
    });

    Route::prefix('registro-preco')->name('registro-preco.')->group(function () {
        Route::get('/', [RegistroPrecoController::class, 'index'])->name('index');
        Route::get('/detalhes/{codigo}', [RegistroPrecoController::class, 'show'])->name('show');
    });

    Route::prefix('requisicao')->name('requisicao.')->group(function () {
        // Por Fornecedor
        Route::get('/fornecedor', [RequisicaoController::class, 'indexFornecedor'])->name('fornecedor.index');
        Route::get('/fornecedor/lista/{exercicio}', [RequisicaoController::class, 'listFornecedor'])->name('fornecedor.list');
        Route::get('/fornecedor/detalhes/{exercicio}/{idfornecedor}', [RequisicaoController::class, 'showFornecedor'])->name('fornecedor.show');

        // Por Solicitante
        Route::get('/solicitante', [RequisicaoController::class, 'indexSolicitante'])->name('solicitante.index');
        Route::get('/solicitante/lista/{exercicio}', [RequisicaoController::class, 'listSolicitante'])->name('solicitante.list');
        Route::get('/solicitante/detalhes/{exercicio}/{idunidade}', [RequisicaoController::class, 'showSolicitante'])->name('solicitante.show');

        // Por Elemento
        Route::get('/elemento', [RequisicaoController::class, 'indexElemento'])->name('elemento.index');
        Route::get('/elemento/lista/{exercicio}', [RequisicaoController::class, 'listElemento'])->name('elemento.list');
        Route::get('/elemento/detalhes/{exercicio}/{idelemento}', [RequisicaoController::class, 'showElemento'])->name('elemento.show');
    });
});

Route::prefix('pessoal/quadro')->name('pessoal.quadro.')->group(function () {
    // Visões principais
    Route::get('/funcao', [QuadroFuncionalController::class, 'porFuncao'])->name('funcao');
    Route::get('/lotacao', [QuadroFuncionalController::class, 'porLotacao'])->name('lotacao');
    Route::get('/regime', [QuadroFuncionalController::class, 'porRegime'])->name('regime');
    Route::get('/servidor', [QuadroFuncionalController::class, 'porServidor'])->name('servidor');

    // Detalhes (chamadas AJAX ou expansão)
    Route::get('/detalhes/{tipo}/{idCategoria}', [QuadroFuncionalController::class, 'showDetalhes'])->name('detalhes');
});

/**
 * Rotas para a ajuda
 */
Route::get('/ajuda/faq', function () {
    return view('ajuda.faq');
})->name('ajuda.faq');
