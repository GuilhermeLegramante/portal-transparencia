<?php

namespace App\Http\Controllers;

use App\Repositories\RequisicaoRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RequisicaoController extends Controller
{
    protected $repository;
    private $idCliente;


    public function __construct(RequisicaoRepository $repository)
    {
        $this->repository = $repository;
        $this->idCliente = config('app.client_id');
    }

    // --- VISÃO: POR FORNECEDOR ---
    public function indexFornecedor()
    {
        $dados = $this->repository->getResumoPorExercicio($this->idCliente);
        return view('compras.requisicao.index', ['dados' => $dados, 'tipo' => 'fornecedor', 'titulo' => 'por Fornecedor']);
    }

    public function listFornecedor($exercicio)
    {
        $dados = $this->repository->getFornecedores($this->idCliente, $exercicio);
        return view('compras.requisicao.fornecedor_list', compact('dados', 'exercicio'));
    }

    public function showFornecedor($exercicio, $idfornecedor)
    {
        $itens = $this->repository->getItensPorFornecedor($this->idCliente, $exercicio, $idfornecedor);
        $header = DB::table('cadmunicipe')->where('id', $idfornecedor)->where('idcliente', $this->idCliente)->first();
        return view('compras.requisicao.show_detalhes', compact('itens', 'header', 'exercicio'));
    }

    // --- VISÃO: POR SOLICITANTE (UNIDADE) ---
    public function indexSolicitante()
    {
        $dados = $this->repository->getResumoPorExercicio($this->idCliente);
        return view('compras.requisicao.index', ['dados' => $dados, 'tipo' => 'solicitante', 'titulo' => 'por Solicitante']);
    }

    public function listSolicitante($exercicio)
    {
        $dados = $this->repository->getSolicitantes($this->idCliente, $exercicio);
        return view('compras.requisicao.solicitante_list', compact('dados', 'exercicio'));
    }

    public function showSolicitante($exercicio, $idunidade)
    {
        // Query de detalhe adaptada do arquivo "Requisição por solicitante - detalhe.sql"
        $itens = DB::table('comrequisicaoitem as item')
            ->join('comrequisicao as requisicao', function ($j) {
                $j->on('requisicao.id', '=', 'item.idrequisicao')->on('requisicao.idcliente', '=', 'item.idcliente');
            })
            ->join('ctbcontadespesa as conta', function ($j) {
                $j->on('conta.id', '=', 'requisicao.iddespesa')->on('conta.idcliente', '=', 'requisicao.idcliente');
            })
            ->select('item.complemento', 'requisicao.data', 'item.quantidade', 'item.valorunitario as valor_unitario')
            ->selectRaw('(item.quantidade * item.valorunitario) AS valor_total')
            ->selectRaw("IF(item.idproduto IS NOT NULL, 
                (SELECT nome FROM almproduto WHERE id = item.idproduto AND idcliente = item.idcliente),
                (SELECT nome FROM comservico WHERE id = item.idservico AND idcliente = item.idcliente)) as nome")
            ->where('item.idcliente', $this->idCliente)
            ->where('requisicao.exercicio', $exercicio)
            ->where('conta.idunidadeorcamentaria', $idunidade)
            ->orderBy('data')->orderBy('nome')->get();

        $header = DB::table('ctbunidadeorcamentaria')->where('id', $idunidade)->where('idcliente', $this->idCliente)->first();
        return view('compras.requisicao.show_detalhes', compact('itens', 'header', 'exercicio'));
    }

    // --- VISÃO: POR ELEMENTO ---
    public function indexElemento()
    {
        $dados = $this->repository->getResumoPorExercicio($this->idCliente);
        return view('compras.requisicao.index', ['dados' => $dados, 'tipo' => 'elemento', 'titulo' => 'por Elemento']);
    }

    public function listElemento($exercicio)
    {
        $dados = $this->repository->getElementos($this->idCliente, $exercicio);
        return view('compras.requisicao.elemento_list', compact('dados', 'exercicio'));
    }

    public function showElemento($exercicio, $idelemento)
    {
        // Query de detalhe adaptada do arquivo "Requisição por elemento - detalhe.sql"
        $itens = DB::table('comrequisicaoitem as item')
            ->join('comrequisicao as requisicao', function ($j) {
                $j->on('requisicao.id', '=', 'item.idrequisicao')->on('requisicao.idcliente', '=', 'item.idcliente');
            })
            ->join('ctbcontadespesa as conta', function ($j) {
                $j->on('conta.id', '=', 'requisicao.iddespesa')->on('conta.idcliente', '=', 'requisicao.idcliente');
            })
            ->select('item.complemento', 'requisicao.data', 'item.quantidade', 'item.valorunitario as valor_unitario')
            ->selectRaw('(item.quantidade * item.valorunitario) AS valor_total')
            ->selectRaw("IF(item.idproduto IS NOT NULL, 
                (SELECT nome FROM almproduto WHERE id = item.idproduto AND idcliente = item.idcliente),
                (SELECT nome FROM comservico WHERE id = item.idservico AND idcliente = item.idcliente)) as nome")
            ->where('item.idcliente', $this->idCliente)
            ->where('requisicao.exercicio', $exercicio)
            ->where('conta.idelemento', $idelemento)
            ->orderBy('data')->orderBy('nome')->get();

        $header = DB::table('ctbelemento')->where('id', $idelemento)->where('idcliente', $this->idCliente)->first();
        return view('compras.requisicao.show_detalhes', compact('itens', 'header', 'exercicio'));
    }
}
