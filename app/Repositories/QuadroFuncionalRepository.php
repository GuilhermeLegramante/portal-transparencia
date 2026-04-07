<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class QuadroFuncionalRepository
{
    // Resumo de contratos por situação
    public function getResumoGeral($idcliente)
    {
        return DB::table('folcontrato as contrato')
            ->join('folsituacaocontratual as situacao', function ($j) {
                $j->on('situacao.id', '=', 'contrato.idsituacao')
                    ->on('situacao.idcliente', '=', 'contrato.idcliente');
            })
            ->select(
                DB::raw('SUM(IF(situacao.codigo = 1, 1, 0)) AS atividade'),
                DB::raw('SUM(IF(situacao.codigo = 2, 1, 0)) AS afastado_nao_calculado'),
                DB::raw('SUM(IF(situacao.codigo = 3, 1, 0)) AS afastado_calculado'),
                DB::raw('SUM(IF(situacao.codigo = 4, 1, 0)) AS exonerado'),
                DB::raw('COUNT(*) AS total')
            )
            ->where('contrato.idcliente', $idcliente)
            ->first();
    }

    // Listagem por Função
    public function getPorFuncao($idcliente, $idsituacao)
    {
        return DB::table('folcontrato as contrato')
            ->join('folfuncao as funcao', function ($j) {
                $j->on('funcao.id', '=', 'contrato.idfuncao')
                    ->on('funcao.idcliente', '=', 'contrato.idcliente');
            })
            ->select('contrato.idfuncao as funcao_id', 'funcao.codigo', 'funcao.nome as descricao')
            ->selectRaw('COUNT(*) AS total')
            ->where('contrato.idcliente', $idcliente)
            ->where('contrato.idsituacao', $idsituacao)
            ->where('contrato.idfuncao', '<>', -1)
            ->groupBy('contrato.idcliente', 'contrato.idfuncao', 'funcao.codigo', 'funcao.nome')
            ->orderBy('funcao.codigo')
            ->get();
    }

    // Listagem por Lotação
    public function getPorLotacao($idcliente, $idsituacao)
    {
        return DB::table('folcontrato as contrato')
            ->join('follotacao as lotacao', 'lotacao.id', '=', 'contrato.idlotacao')
            ->join('ctbunidadeorcamentaria as unidade', 'unidade.id', '=', 'lotacao.idunidade')
            ->select('contrato.idlotacao as lotacao_id', 'unidade.nome as unidade', 'lotacao.codigo', 'lotacao.nome as descricao')
            ->selectRaw('COUNT(*) AS total')
            ->where('contrato.idcliente', $idcliente)
            ->where('contrato.idsituacao', $idsituacao)
            ->groupBy('contrato.idcliente', 'contrato.idlotacao', 'unidade.nome', 'lotacao.codigo', 'lotacao.nome')
            ->get();
    }

    // --- LISTAGEM POR REGIME ---
    public function getPorRegime($idcliente, $idsituacao)
    {
        return DB::table('folcontrato as contrato')
            ->join('folregime as regime', function ($j) {
                $j->on('regime.id', '=', 'contrato.idregime')
                    ->on('regime.idcliente', '=', 'contrato.idcliente');
            })
            ->select(
                'contrato.idregime as regime_id',
                'regime.codigo',
                'regime.nome as descricao',
                'contrato.idcliente'
            )
            ->selectRaw('COUNT(*) AS total')
            ->where('contrato.idcliente', $idcliente)
            ->where('contrato.idsituacao', $idsituacao)
            ->where('contrato.idregime', '<>', -1)
            ->groupBy('contrato.idcliente', 'contrato.idregime', 'regime.codigo', 'regime.nome')
            ->orderBy('regime.codigo')
            ->get();
    }

    // --- RELAÇÃO NOMINAL (PARA O MENU "POR SERVIDOR") ---
    public function getRelacaoNominal($idcliente, $idsituacao)
    {
        return DB::table('folcontrato as contrato')
            ->join('cadmunicipe as municipe', function ($j) {
                $j->on('municipe.id', '=', 'contrato.idservidor')
                    ->on('municipe.idcliente', '=', 'contrato.idcliente');
            })
            ->select(
                'contrato.idcliente',
                'contrato.matricula',
                'contrato.admissao as data_admissao',
                'municipe.nome',
                // Subqueries para evitar erros de GROUP BY em listagens simples
                DB::raw("(SELECT f.nome FROM folfuncao f WHERE f.id = contrato.idfuncao AND f.idcliente = contrato.idcliente) as funcao"),
                DB::raw("(SELECT l.nome FROM follotacao l WHERE l.id = contrato.idlotacao AND l.idcliente = contrato.idcliente) as lotacao")
            )
            ->where('contrato.idcliente', $idcliente)
            ->where('contrato.idsituacao', $idsituacao)
            ->orderBy('municipe.nome')
            ->get();
    }

    /**
     * MÉTODO DE DETALHAMENTO (Usado para Função, Lotação ou Regime)
     * Retorna os servidores específicos de uma categoria selecionada
     */
    public function getDetalhesContratos($idcliente, $idsituacao, $campoFiltro, $idFiltro)
    {
        return DB::table('folcontrato as contrato')
            ->join('cadmunicipe as municipe', function ($j) {
                $j->on('municipe.id', '=', 'contrato.idservidor')
                    ->on('municipe.idcliente', '=', 'contrato.idcliente');
            })
            ->select(
                'contrato.matricula',
                'contrato.admissao as data_admissao',
                'municipe.nome',
                'municipe.id as municipe_id'
            )
            ->where('contrato.idcliente', $idcliente)
            ->where('contrato.idsituacao', $idsituacao)
            ->where("contrato.{$campoFiltro}", $idFiltro)
            ->orderBy('municipe.nome')
            ->get();
    }
}
