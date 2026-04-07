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
        $this->idCliente = config('app.client_id');
    }

    /**
     * Exibe o resumo por exercício (Nível 1)
     */
    public function index()
    {
        $resumoAnual = $this->repository->getResumoLicitacoesPorExercicio($this->idCliente);

        return view('compras.licitacoes.processo.index', compact('resumoAnual'));
    }

    /**
     * Exibe a listagem de licitações do ano (Nível 2)
     */
    public function list($exercicio)
    {
        $dados = $this->repository->getLicitacoesPorExercicio($this->idCliente, $exercicio);

        return view('compras.licitacoes.processo.list', compact('dados', 'exercicio'));
    }

    /**
     * Exibe os detalhes de uma licitação específica (Nível 3 - Opcional)
     */
    public function show($id)
    {
        $licitacao  = $this->repository->getLicitacaoDetalhes($this->idCliente, $id);
        $comissao   = $this->repository->getComissaoLicitacao($this->idCliente, $id);
        $itens      = $this->repository->getItensLicitacao($this->idCliente, $id);
        $vencedores = $this->repository->getVencedoresLicitacao($this->idCliente, $id);

        return view('compras.licitacoes.processo.show', compact('licitacao', 'comissao', 'itens', 'vencedores'));
    }
}
