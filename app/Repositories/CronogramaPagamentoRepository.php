<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class CronogramaPagamentoRepository
{
    /**
     * Relação resumida por recurso vinculado (Cards ou Tabela de Resumo)
     */
    public function getResumoPorRecurso($idcliente, $exercicio)
    {
        return DB::table('ctbcronogramapagamento as cronograma')
            ->join('ctbempenho as empenho', function ($j) {
                $j->on('empenho.id', '=', 'cronograma.idempenho')
                    ->on('empenho.idcliente', '=', 'cronograma.idcliente');
            })
            ->join('ctbrecursovinculado as recurso', function ($j) {
                $j->on('recurso.id', '=', 'empenho.idrecurso')
                    ->on('recurso.idcliente', '=', 'empenho.idcliente');
            })
            ->select(
                'recurso.id as recurso_id',
                'recurso.codigo as codigo_recurso',
                'recurso.nome as descricao_recurso'
            )
            ->selectRaw("SUM(cronograma.valor) AS valor_liquidado")
            ->selectRaw("SUM(cronograma.valorpago) AS valor_pago")
            ->selectRaw("SUM(cronograma.valor - cronograma.valorpago) AS saldo_pagar")
            ->where('cronograma.idcliente', $idcliente)
            ->where('empenho.exercicio', $exercicio)
            ->where('empenho.idcredor', '<>', -1)
            ->groupBy('recurso.id', 'recurso.codigo', 'recurso.nome')
            ->orderBy('recurso.codigo')
            ->get();
    }

    /**
     * Listagem detalhada dos empenhos no cronograma
     */
    public function getListagemCompleta($idcliente, $exercicio, $credorId = null, $recursoId = null)
    {
        $query = DB::table('ctbcronogramapagamento as cronograma')
            ->join('ctbempenho as empenho', function ($j) {
                $j->on('empenho.id', '=', 'cronograma.idempenho')
                    ->on('empenho.idcliente', '=', 'cronograma.idcliente');
            })
            ->join('ctbrecursovinculado as recurso', function ($j) {
                $j->on('recurso.id', '=', 'empenho.idrecurso')
                    ->on('recurso.idcliente', '=', 'empenho.idcliente');
            })
            ->select(
                'cronograma.liquidacao as data_liquidacao',
                'cronograma.pagamento as data_vencimento',
                'empenho.numero as empenho_numero',
                'cronograma.notafiscal as nota_fiscal',
                'recurso.codigo as codigo_recurso',
                'recurso.nome as descricao_recurso',
                'cronograma.justificativa',
                'cronograma.valor',
                'cronograma.pago as pagamento_realizado'
            )
            ->selectRaw("(SELECT nome FROM cadmunicipe WHERE id = empenho.idcredor AND idcliente = empenho.idcliente) as nome_credor")
            ->where('cronograma.idcliente', $idcliente)
            ->where('empenho.exercicio', $exercicio);

        if ($credorId) {
            $query->where('empenho.idcredor', $credorId);
        }

        if ($recursoId) {
            $query->where('empenho.idrecurso', $recursoId);
        }

        return $query->orderBy('recurso.codigo')
            ->orderBy('cronograma.pagamento')
            ->get();
    }
}
