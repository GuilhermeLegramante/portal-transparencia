<?php

namespace App\Http\Controllers;

use App\Repositories\PatrimonioRepository;
use Illuminate\Http\Request;

class PatrimonioController extends Controller
{
    protected $repo;
    private $idCliente;

    public function __construct(PatrimonioRepository $repo)
    {
        $this->repo = $repo;
        $this->idCliente = config('app.client_id');
    }

    public function index(Request $request)
    {
        $filtrosAtivos = $request->only(['classe', 'especie', 'classificacao']);
        $opcoesFiltros = $this->repo->getFiltros($this->idCliente);
        $patrimonios = $this->repo->getPatrimonios($this->idCliente, $filtrosAtivos);

        $breadcrumb = ['Patrimônio' => '#', 'Listagem de Bens' => ''];

        return view('patrimonio.index', compact('patrimonios', 'opcoesFiltros', 'filtrosAtivos', 'breadcrumb'));
    }

    public function show($id)
    {
        $detalhes = $this->repo->getDetalhes($this->idCliente, $id);

        if (!$detalhes) return redirect()->back()->with('error', 'Bem não encontrado.');

        $breadcrumb = ['Patrimônio' => route('patrimonio.index'), 'Detalhes do Bem' => ''];

        return view('patrimonio.show', array_merge($detalhes, ['breadcrumb' => $breadcrumb]));
    }
}