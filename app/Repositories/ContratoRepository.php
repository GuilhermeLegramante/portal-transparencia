<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class ContratoRepository
{
    public function getResumoContratosPorExercicio($idcliente)
    {
        // Subquery para pegar a data de início (código 1)
        $ativacao = DB::table('liccontratoocorrencia')
            ->select('idcontrato', DB::raw('YEAR(datainicio) as ano_inicio'))
            ->where('idcliente', $idcliente)
            ->where('codigo', 1);

        return DB::table('liccontrato as contrato')
            ->joinSub($ativacao, 'ativ', function ($join) {
                $join->on('contrato.id', '=', 'ativ.idcontrato');
            })
            ->select('ativ.ano_inicio as exercicio')
            ->selectRaw("SUM(IF(contrato.situacao = 'ATV', 1, 0)) AS ativo")
            ->selectRaw("SUM(IF(contrato.situacao = 'RES', 1, 0)) AS rescindido")
            ->selectRaw("SUM(IF(contrato.situacao = 'SUS', 1, 0)) AS suspenso")
            ->selectRaw("SUM(IF(contrato.situacao = 'ANL', 1, 0)) AS anulado")
            ->selectRaw("SUM(IF(contrato.situacao = 'ENC', 1, 0)) AS encerrado")
            ->selectRaw("COUNT(*) AS total")
            ->where('contrato.idcliente', $idcliente)
            ->groupBy('exercicio')
            ->orderBy('exercicio', 'DESC')
            ->get();
    }

    public function getContratosPorExercicio($idcliente, $exercicio)
    {
        return DB::table('liccontratoocorrencia as ocorrencia')
            ->join('liccontrato as contrato', function ($join) {
                $join->on('contrato.id', '=', 'ocorrencia.idcontrato')
                    ->on('contrato.idcliente', '=', 'ocorrencia.idcliente');
            })
            ->leftJoin('cadmunicipe as municipe', function ($join) {
                $join->on('contrato.idfornecedor', '=', 'municipe.id')
                    ->on('contrato.idcliente', '=', 'municipe.idcliente');
            })
            ->select(
                'contrato.id',
                'contrato.numero',
                'municipe.nome as nome_fornecedor',
                'ocorrencia.dataassinatura',
                'ocorrencia.datainicio',
                'contrato.situacao',
                'ocorrencia.valor',
                DB::raw("(SELECT MAX(mov.datatermino) FROM liccontratoocorrencia mov 
                          WHERE mov.idcontrato = contrato.id AND mov.idcliente = contrato.idcliente 
                          AND mov.codigo IN (1, 9)) as data_termino")
            )
            ->where('contrato.idcliente', $idcliente)
            ->whereRaw('YEAR(ocorrencia.datainicio) = ?', [$exercicio])
            ->where('ocorrencia.codigo', 1)
            ->orderBy('contrato.numero')
            ->get();
    }

    public function getContratoDetalhes($idcliente, $id)
    {
        return DB::table('liccontrato as contrato')
            ->leftJoin('cadmunicipe as municipe', function ($join) {
                $join->on('contrato.idfornecedor', '=', 'municipe.id')
                    ->on('contrato.idcliente', '=', 'municipe.idcliente');
            })
            ->select('contrato.*', 'municipe.nome as fornecedor_nome')
            ->where('contrato.idcliente', $idcliente)
            ->where('contrato.id', $id)
            ->first();
    }

    public function getOcorrenciasContrato($idcliente, $idcontrato)
    {
        return DB::table('liccontratoocorrencia')
            ->where('idcliente', $idcliente)
            ->where('idcontrato', $idcontrato)
            ->orderBy('datainicio', 'ASC')
            ->get();
    }
}
