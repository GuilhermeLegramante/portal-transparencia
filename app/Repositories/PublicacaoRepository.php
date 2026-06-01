<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class PublicacaoRepository
{
    public function getPublicacoesCompletas($idcliente, $exercicio, $categoriaSelecionada = null)
    {
        // 1. Consulta para Prestações de Contas
        $prestacaoContas = DB::table('pubprestacaoconta as publicacao')
            ->select(
                'publicacao.idcliente as cliente_id',
                'publicacao.exercicio',
                'publicacao.id as codigo',
                DB::raw('UPPER(publicacao.descricao) as descricao'),
                DB::raw("'prestacao' as tipo"), // <--- INJETADO: Identifica que é prestação de contas
                'publicacao.datahora as data',
                DB::raw('UPPER(publicacao.categoria) as categoria'),
                'publicacao.path as caminho_arquivo'
            )
            ->where('publicacao.idcliente', $idcliente)
            ->where('publicacao.exercicio', $exercicio);

        // 2. Consulta para Publicações Gerais com subquery de Tags
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
                'publicacao.path as caminho_arquivo',
                DB::raw("'geral' as tipo"), // <--- INJETADO: Identifica que é publicação geral

            )
            ->where('publicacao.idcliente', $idcliente)
            ->where('publicacao.exercicio', $exercicio);

        // Aplica o filtro se o utilizador escolheu uma categoria
        if (!empty($categoriaSelecionada)) {
            // Filtro direto na coluna de texto da Prestação de Contas
            $prestacaoContas->whereRaw('UPPER(publicacao.categoria) = ?', [strtoupper($categoriaSelecionada)]);

            // Filtro via tabela pivot (pubpublicacaotag) para as Publicações Gerais
            $publicacoesGerais->whereExists(function ($query) use ($categoriaSelecionada, $idcliente) {
                $query->select(DB::raw(1))
                    ->from('pubpublicacaotag as pt')
                    ->join('pubtag as t', 't.id', '=', 'pt.idtag')
                    ->whereColumn('pt.idpublicacao', 'publicacao.id')
                    ->where('pt.idcliente', $idcliente)
                    ->whereRaw('UPPER(t.nome) = ?', [strtoupper($categoriaSelecionada)]);
            });
        }

        // Une os blocos e ordena por data decrescente
        return $prestacaoContas->unionAll($publicacoesGerais)
            ->orderBy('data', 'desc')
            ->get();
    }
}
