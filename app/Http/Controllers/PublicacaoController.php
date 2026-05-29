<?php

namespace App\Http\Controllers;

use App\Repositories\PublicacaoRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PublicacaoController extends Controller
{
    protected $repo;

    public function __construct(PublicacaoRepository $repo)
    {
        $this->repo = $repo;
    }

    public function index(Request $request)
    {
        // 1. Captura os filtros da URL (via GET)
        $exercicio = $request->get('exercicio', date('Y'));
        $categoria = $request->get('categoria');

        // 2. ID do cliente definido dinamicamente pelo seu Middleware de Tenant
        $idcliente = config('app.client_id');

        // 3. Executa a query principal passando os filtros
        $publicacoes = $this->repo->getPublicacoesCompletas($idcliente, $exercicio, $categoria);

        // 4. Carrega o dropdown com o mapeamento correto das tabelas
        $categoriasDisponiveis = $this->getCategoriasDisponiveis($idcliente);

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

    /**
     * Une e filtra as categorias/tags que pertencem estritamente a este cliente
     */
    private function getCategoriasDisponiveis($idcliente)
    {
        // Categorias em formato texto da tabela de prestação de contas
        $categoriasPrestacao = DB::table('pubprestacaoconta')
            ->where('idcliente', $idcliente)
            ->whereNotNull('categoria')
            ->distinct()
            ->pluck('categoria')
            ->toArray();

        // Tags associadas a este cliente através da tabela pivot pubpublicacaotag
        $tagsGerais = DB::table('pubpublicacaotag as pt')
            ->join('pubtag as t', 't.id', '=', 'pt.idtag')
            ->where('pt.idcliente', $idcliente)
            ->distinct()
            ->pluck('t.nome')
            ->toArray();

        // Mescla, remove duplicados, padroniza em UPPERCASE e ordena alfabeticamente
        $todas = array_unique(array_merge($categoriasPrestacao, $tagsGerais));
        $todas = array_map('strtoupper', $todas);
        sort($todas);

        return $todas;
    }
}
