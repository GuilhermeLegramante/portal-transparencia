<?php

namespace App\Http\Controllers;

use App\Repositories\ContratoRepository;

class ContratoController extends Controller
{
    protected $repository;
    private $idCliente;

    public function __construct(ContratoRepository $repository)
    {
        $this->repository = $repository;
        $this->idCliente = config('app.client_id');
    }

    public function index()
    {
        $resumoAnual = $this->repository->getResumoContratosPorExercicio($this->idCliente);
        return view('compras.licitacoes.contrato.index', compact('resumoAnual'));
    }

    public function list($exercicio)
    {
        $dados = $this->repository->getContratosPorExercicio($this->idCliente, $exercicio);
        return view('compras.licitacoes.contrato.list', compact('dados', 'exercicio'));
    }

    public function show($id)
    {
        $idcliente = $this->idCliente;
        $contrato = $this->repository->getContratoDetalhes($idcliente, $id);
        $ocorrencias = $this->repository->getOcorrenciasContrato($idcliente, $id);

        return view('compras.licitacoes.contrato.show', compact('contrato', 'ocorrencias'));
    }
}
