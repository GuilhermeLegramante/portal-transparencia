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

    private function getCategoriasDisponiveis($idcliente)
    {
        // 1. Categorias vindas das prestações de contas
        $categoriasPrestacao = DB::table('pubprestacaoconta')
            ->where('idcliente', $idcliente)
            ->whereNotNull('categoria')
            ->distinct()
            ->pluck('categoria')
            ->toArray();

        // 2. Tags vinculadas a publicações gerais deste cliente
        $tagsGerais = DB::table('pubpublicacaotag as pt')
            ->join('pubtag as t', 't.id', '=', 'pt.idtag')
            ->where('pt.idcliente', $idcliente)
            ->distinct()
            ->pluck('t.nome')
            ->toArray();

        // Juntar as duas listas originais
        $brutas = array_merge($categoriasPrestacao, $tagsGerais);

        $limpas = [];
        foreach ($brutas as $cat) {
            // Converte para maiúsculas
            $catFormatada = mb_strtoupper($cat, 'UTF-8');

            // Remove espaços em branco antes/depois e limpa espaços duplos no meio da string
            $catFormatada = trim(preg_replace('/\s+/', ' ', $catFormatada));

            if (!empty($catFormatada)) {
                $limpas[] = $catFormatada;
            }
        }

        // Remove os duplicados reais após a limpeza profunda
        $todas = array_unique($limpas);

        // Ordena alfabeticamente de A a Z
        sort($todas);

        return $todas;
    }
}
