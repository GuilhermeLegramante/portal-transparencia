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
        
    }

    public function index(Request $request)
    {
        $exercicio = $request->get('exercicio', date('Y'));

        $publicacoes = $this->repo->getPublicacoesCompletas(config('app.client_id'), $exercicio);

        $breadcrumb = [
            'Transparência' => '#',
            'Publicações e Documentos' => ''
        ];

        return view('transparencia.publicacoes.index', compact('publicacoes', 'exercicio', 'breadcrumb'));
    }
}
