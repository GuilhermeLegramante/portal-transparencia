<?php

namespace App\Http\Controllers;

use App\Repositories\LicitacaoRepository;
use Illuminate\Http\Request;

class LicitacaoController extends Controller
{
    protected $repository;
    private $idCliente;

    public function __construct(LicitacaoRepository $repository)
    {
        $this->repository = $repository;
        
    }

    /**
     * Exibe o resumo por exercício (Nível 1)
     */
    public function index()
    {
        $resumoAnual = $this->repository->getResumoLicitacoesPorExercicio(config('app.client_id'));

        return view('compras.licitacoes.processo.index', compact('resumoAnual'));
    }

    /**
     * Exibe a listagem de licitações do ano (Nível 2)
     */
    public function list($exercicio)
    {
        $dados = $this->repository->getLicitacoesPorExercicio(config('app.client_id'), $exercicio);

        return view('compras.licitacoes.processo.list', compact('dados', 'exercicio'));
    }

    /**
     * Exibe os detalhes de uma licitação específica (Nível 3 - Opcional)
     */
    public function show($id)
    {
        $licitacao  = $this->repository->getLicitacaoDetalhes(config('app.client_id'), $id);
        $comissao   = $this->repository->getComissaoLicitacao(config('app.client_id'), $id);
        $itens      = $this->repository->getItensLicitacao(config('app.client_id'), $id);
        $vencedores = $this->repository->getVencedoresLicitacao(config('app.client_id'), $id);

        return view('compras.licitacoes.processo.show', compact('licitacao', 'comissao', 'itens', 'vencedores'));
    }
}
