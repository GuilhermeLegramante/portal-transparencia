<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class RequisicaoRepository
{
    // Resumo por Exercício (Comum a todos)
    // --- RESUMO POR EXERCÍCIO ---
    public function getResumoPorExercicio($idcliente)
    {
        return DB::table('comrequisicao as requisicao')
            ->leftJoin('comrequisicaoitem as item', function ($join) {
                $join->on('item.idrequisicao', '=', 'requisicao.id')
                    ->on('item.idcliente', '=', 'requisicao.idcliente');
            })
            ->select(
                'requisicao.exercicio',
                'requisicao.idcliente',
                DB::raw('COUNT(DISTINCT requisicao.id) AS quantidade'),
                DB::raw('SUM(item.quantidade * item.valorunitario) AS valor_total')
            )
            ->where('requisicao.idcliente', $idcliente)
            ->groupBy('requisicao.exercicio', 'requisicao.idcliente') // idcliente incluído aqui
            ->orderBy('requisicao.exercicio', 'DESC')
            ->get();
    }

    // --- POR SOLICITANTE ---
    public function getSolicitantes($idcliente, $exercicio)
    {
        return DB::table('comrequisicao as requisicao')
            ->join('ctbcontadespesa as conta', function ($j) {
                $j->on('conta.id', '=', 'requisicao.iddespesa')
                    ->on('conta.idcliente', '=', 'requisicao.idcliente');
            })
            ->leftJoin('comrequisicaoitem as item', function ($j) {
                $j->on('item.idrequisicao', '=', 'requisicao.id')
                    ->on('item.idcliente', '=', 'requisicao.idcliente');
            })
            ->select(
                'requisicao.exercicio',
                'conta.idunidadeorcamentaria as unidade_id',
                'requisicao.idcliente',
                DB::raw("(SELECT nome FROM ctbunidadeorcamentaria WHERE id = conta.idunidadeorcamentaria AND idcliente = conta.idcliente) as nome"),
                DB::raw('COUNT(DISTINCT requisicao.id) AS quantidade_requisicao'),
                DB::raw('SUM(item.quantidade * item.valorunitario) AS total_requisitado')
            )
            ->where('requisicao.idcliente', $idcliente)
            ->where('requisicao.exercicio', $exercicio)
            ->groupBy('requisicao.exercicio', 'conta.idunidadeorcamentaria', 'requisicao.idcliente', 'conta.idcliente') // idcliente incluído aqui
            ->orderBy('nome')
            ->get();
    }

    // --- FORNECEDOR ---
    public function getFornecedores($idcliente, $exercicio)
    {
        // Query baseada em Requisição por fornecedor.sql
        return DB::table('comrequisicaoitem as item')
            ->join('comrequisicao as requisicao', function ($j) {
                $j->on('requisicao.id', '=', 'item.idrequisicao')->on('requisicao.idcliente', '=', 'item.idcliente');
            })
            ->select('item.idfornecedor as municipe_id', 'requisicao.exercicio')
            ->selectRaw('(SELECT nome FROM cadmunicipe WHERE id = item.idfornecedor AND idcliente = item.idcliente) as nome')
            ->selectRaw('COUNT(DISTINCT item.idrequisicao) AS quantidade_requisicao')
            ->selectRaw('SUM(item.quantidade * item.valorunitario) AS total_requisitado')
            ->where('item.idcliente', $idcliente)
            ->where('requisicao.exercicio', $exercicio)
            ->groupBy('item.idcliente', 'requisicao.exercicio', 'item.idfornecedor')
            ->orderBy('nome')
            ->get();
    }

    public function getItensPorFornecedor($idcliente, $exercicio, $idfornecedor)
    {
        // Query baseada em Requisição por fornecedor - item.sql
        return DB::table('comrequisicaoitem as item')
            ->join('comrequisicao as requisicao', function ($j) {
                $j->on('requisicao.id', '=', 'item.idrequisicao')->on('requisicao.idcliente', '=', 'item.idcliente');
            })
            ->select('item.complemento', 'requisicao.data', 'item.quantidade', 'item.valorunitario as valor_unitario')
            ->selectRaw('(item.quantidade * item.valorunitario) AS valor_total')
            ->selectRaw("IF(item.idproduto IS NOT NULL, 
                (SELECT nome FROM almproduto WHERE id = item.idproduto AND idcliente = item.idcliente),
                (SELECT nome FROM comservico WHERE id = item.idservico AND idcliente = item.idcliente)) as nome")
            ->where('item.idcliente', $idcliente)
            ->where('requisicao.exercicio', $exercicio)
            ->where('item.idfornecedor', $idfornecedor)
            ->orderBy('data')->orderBy('nome')
            ->get();
    }

    // --- ELEMENTO DE DESPESA ---
    // --- POR ELEMENTO ---
    public function getElementos($idcliente, $exercicio)
    {
        return DB::table('comrequisicao as requisicao')
            ->join('ctbcontadespesa as conta', function ($j) {
                $j->on('conta.id', '=', 'requisicao.iddespesa')
                    ->on('conta.idcliente', '=', 'requisicao.idcliente');
            })
            ->leftJoin('comrequisicaoitem as item', function ($j) {
                $j->on('item.idrequisicao', '=', 'requisicao.id')
                    ->on('item.idcliente', '=', 'requisicao.idcliente');
            })
            ->select(
                'requisicao.exercicio',
                'conta.idelemento as elemento_id',
                'requisicao.idcliente',
                // Usamos MAX() para que o MySQL entenda que haverá apenas um resultado de nome por grupo
                DB::raw("MAX((SELECT elemento.nome FROM ctbelemento elemento WHERE elemento.id = conta.idelemento AND elemento.idcliente = conta.idcliente)) as nome"),
                DB::raw('COUNT(DISTINCT requisicao.id) AS quantidade_requisicao'),
                DB::raw('SUM(item.quantidade * item.valorunitario) AS total_requisitado')
            )
            ->where('requisicao.idcliente', $idcliente)
            ->where('requisicao.exercicio', $exercicio)
            ->groupBy('requisicao.exercicio', 'conta.idelemento', 'requisicao.idcliente', 'conta.idcliente')
            ->orderBy('nome')
            ->get();
    }
}
