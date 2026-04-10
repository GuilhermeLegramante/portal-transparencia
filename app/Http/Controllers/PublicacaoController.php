<?php

namespace App\Http\Controllers;

use App\Repositories\PublicacaoRepository;
use Illuminate\Http\Request;

class PublicacaoController extends Controller
{
    protected $repo;
    private $idCliente;

    public function __construct(PublicacaoRepository $repo)
    {
        $this->repo = $repo;
        $this->idCliente = config('app.client_id');
    }

    public function index(Request $request)
    {
        $exercicio = $request->get('exercicio', date('Y'));

        $publicacoes = $this->repo->getPublicacoesCompletas($this->idCliente, $exercicio);

        $breadcrumb = [
            'Transparência' => '#',
            'Publicações e Documentos' => ''
        ];

        return view('transparencia.publicacoes.index', compact('publicacoes', 'exercicio', 'breadcrumb'));
    }
}
