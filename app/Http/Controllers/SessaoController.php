<?php

namespace App\Http\Controllers;

use App\Repositories\SessaoRepository;
use App\Repositories\ParlamentarRepository;
use Illuminate\Http\Request;

class SessaoController extends Controller
{
    protected $repo;
    protected $pRep;

    public function __construct(SessaoRepository $repo, ParlamentarRepository $pRep)
    {
        $this->repo = $repo;
        $this->pRep = $pRep;
    }

    public function index(Request $request)
    {
        $idcliente = config('app.client_id');
        $exercicio = $request->get('exercicio', date('Y'));
        $legislaturas = $this->pRep->getLegislaturas($idcliente);

        $sessoes = $this->repo->getSessoes($idcliente, $exercicio, $request->tipo, $request->legislatura);

        return view('parlamentar.sessao.index', compact('sessoes', 'legislaturas', 'exercicio'));
    }

    public function show($id)
    {
        $idcliente = config('app.client_id');
        $dados = $this->repo->getDetalhesSessao($idcliente, $id);
        return view('parlamentar.sessao.show', $dados);
    }

    public function projeto($idProtocolo)
    {
        $idcliente = config('app.client_id');
        $dados = $this->repo->getDetalheProjeto($idcliente, $idProtocolo);
        return view('parlamentar.sessao.projeto', $dados);
    }
}
