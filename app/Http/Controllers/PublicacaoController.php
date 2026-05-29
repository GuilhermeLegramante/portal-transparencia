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
        $categoria = $request->get('categoria'); // Captura o filtro de categoria

        $idcliente = config('app.client_id');

        // Busca publicações filtradas
        $publicacoes = $this->repo->getPublicacoesCompletas($idcliente, $exercicio, $categoria);

        // Busca todas as categorias existentes do cliente para popular o select
        $categoriasDisponiveis = $this->repo->getCategoriasDisponiveis($idcliente);

        $breadcrumb = [
            'Transparência' => '#',
            'Publicações e Documentos' => ''
        ];

        return view('transparencia.publicacoes.index', compact(
            'publicacoes',
            'exercicio',
            'categoria',
            'categoriasDisponiveis',
            'breadcrumb'
        ));
    }
}
