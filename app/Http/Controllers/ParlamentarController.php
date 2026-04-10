<?php

namespace App\Http\Controllers;

use App\Repositories\ParlamentarRepository;
use Illuminate\Http\Request;

class ParlamentarController extends Controller
{
    protected $repo;
    public function __construct(ParlamentarRepository $repo)
    {
        $this->repo = $repo;
    }

    public function index(Request $request)
    {
        $idcliente = config('app.client_id');
        $legislaturas = $this->repo->getLegislaturas($idcliente);
        $legSelecionada = $request->get('legislatura', $legislaturas->first()->id ?? null);

        $parlamentares = $legSelecionada ? $this->repo->getParlamentaresPorLegislatura($idcliente, $legSelecionada) : collect();
        $mesas = $legSelecionada ? $this->repo->getMesaDiretora($idcliente, $legSelecionada) : collect();

        return view('parlamentar.index', compact('legislaturas', 'parlamentares', 'mesas', 'legSelecionada'));
    }
}
