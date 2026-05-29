<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class PublicacaoRepository
{
    public function getPublicacoesCompletas($idcliente, $exercicio)
    {
        // Consulta para Prestações de Contas
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

        // Consulta para Publicações Gerais com subquery de Tags
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

        // Une os resultados e ordena por data decrescente
        return $prestacaoContas->unionAll($publicacoesGerais)
            ->orderBy('data', 'desc')
            ->get();
    }
}
