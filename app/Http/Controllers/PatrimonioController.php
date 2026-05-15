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
        
    }

    public function index(Request $request)
    {
        $filtrosAtivos = $request->only(['classe', 'especie', 'classificacao']);
        $opcoesFiltros = $this->repo->getFiltros(config('app.client_id'));
        $patrimonios = $this->repo->getPatrimonios(config('app.client_id'), $filtrosAtivos);

        $breadcrumb = ['Patrimônio' => '#', 'Listagem de Bens' => ''];

        return view('patrimonio.index', compact('patrimonios', 'opcoesFiltros', 'filtrosAtivos', 'breadcrumb'));
    }

    public function show($id)
    {
        $detalhes = $this->repo->getDetalhes(config('app.client_id'), $id);

        if (!$detalhes) return redirect()->back()->with('error', 'Bem não encontrado.');

        $breadcrumb = ['Patrimônio' => route('patrimonio.index'), 'Detalhes do Bem' => ''];

        return view('patrimonio.show', array_merge($detalhes, ['breadcrumb' => $breadcrumb]));
    }
}