<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class PublicacaoRepository
{
    public function getCategoriasDisponiveis($idcliente)
    {
        // Captura as categorias fixas das prestações de contas
        $categoriasPrestacao = DB::table('pubprestacaoconta')
            ->where('idcliente', $idcliente)
            ->whereNotNull('categoria')
            ->distinct()
            ->pluck('categoria')
            ->toArray();

        // Captura as tags vinculadas às publicações gerais
        $tagsGerais = DB::table('pubtag')
            ->where('idcliente', $idcliente)
            ->distinct()
            ->pluck('nome')
            ->toArray();

        // Une as duas fontes, remove duplicados, padroniza em caixa alta e ordena
        $todas = array_unique(array_merge($categoriasPrestacao, $tagsGerais));
        $todas = array_map('strtoupper', $todas);
        sort($todas);

        return $todas;
    }

    public function getPublicacoesCompletas($idcliente, $exercicio, $categoriaSelecionada = null)
    {
        // 1. Consulta para Prestações de Contas
        $prestacaoContas = DB::table('pubprestacaoconta as publicacao')
            ->select(
                'publicacao.idcliente as cliente_id',
                'publicacao.exercicio',
                'publicacao.id as codigo',
                DB::raw('UPPER(publicacao.descricao) as descricao'),
                'publicacao.datahora as data',
                DB::raw('UPPER(publicacao.categoria) as categoria'),
                'publicacao.path as caminho_arquivo'
            )
            ->where('publicacao.idcliente', $idcliente)
            ->where('publicacao.exercicio', $exercicio);

        // 2. Consulta para Publicações Gerais
        $publicacoesGerais = DB::table('pubpublicacao as publicacao')
            ->select(
                'publicacao.idcliente as cliente_id',
                'publicacao.exercicio',
                'publicacao.id as codigo',
                DB::raw('UPPER(publicacao.descricao) as descricao'),
                'publicacao.datahora as data',
                DB::raw("(SELECT UPPER(GROUP_CONCAT(DISTINCT tag.nome ORDER BY tag.nome SEPARATOR ';'))
                      FROM pubpublicacaotag categoria
                      INNER JOIN pubtag tag ON tag.id = categoria.idtag
                      WHERE categoria.idpublicacao = publicacao.id
                      AND categoria.idcliente = publicacao.idcliente
                      GROUP BY categoria.idcliente, categoria.idpublicacao) as categoria"),
                'publicacao.path as caminho_arquivo'
            )
            ->where('publicacao.idcliente', $idcliente)
            ->where('publicacao.exercicio', $exercicio);

        // Se houver uma categoria selecionada no filtro, aplica em ambas antes da união
        if (!empty($categoriaSelecionada)) {
            // Na prestação de contas, a filtragem é direta na coluna
            $prestacaoContas->whereRaw('UPPER(publicacao.categoria) = ?', [strtoupper($categoriaSelecionada)]);

            // Nas publicações gerais, filtramos verificando se a subquery de tags conteria a tag buscada
            $publicacoesGerais->whereExists(function ($query) use ($categoriaSelecionada) {
                $query->select(DB::raw(1))
                    ->from('pubpublicacaotag as pt')
                    ->join('pubtag as t', 't.id', '=', 'pt.idtag')
                    ->whereColumn('pt.idpublicacao', 'publicacao.id')
                    ->whereColumn('pt.idcliente', 'publicacao.idcliente')
                    ->whereRaw('UPPER(t.nome) = ?', [strtoupper($categoriaSelecionada)]);
            });
        }

        return $prestacaoContas->unionAll($publicacoesGerais)
            ->orderBy('data', 'desc')
            ->get();
    }
}
