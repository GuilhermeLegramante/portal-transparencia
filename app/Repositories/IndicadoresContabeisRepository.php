<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class IndicadoresContabeisRepository
{
    /**
     * Retorna o resumo geral anualizado das despesas (Exercício Atual vs Anterior)
     */
    public function getResumoGeralDespesa($idcliente, $exercicio)
    {
        $sql = "
            SELECT query.*,
                   query.valor_orcado_anterior + query.valor_remanejado_anterior AS valor_atualizado_anterior,
                   query.valor_orcado_exercicio + query.valor_remanejado_exercicio AS valor_atualizado_exercicio
              FROM (SELECT movimento.idcliente AS idcliente,
                           (SELECT SUM(loa.total)
                              FROM ctbcontadespesaloa loa
                                   INNER JOIN ctbcontadespesa conta
                                      ON loa.iddespesa = conta.id AND loa.idcliente = conta.idcliente
                             WHERE loa.idcliente = empenho.idcliente
                               AND conta.exercicio = :exe1 - 1
                             GROUP BY loa.idcliente) AS valor_orcado_anterior,
                           (SELECT SUM(loa.total)
                              FROM ctbcontadespesaloa loa
                                   INNER JOIN ctbcontadespesa conta
                                      ON loa.iddespesa = conta.id AND loa.idcliente = conta.idcliente
                             WHERE loa.idcliente = empenho.idcliente
                               AND conta.exercicio = :exe2
                             GROUP BY loa.idcliente) AS valor_orcado_exercicio,
                           IFNULL(
                              (SELECT SUM(IF(remanejo.operacao = 'S', remanejo.total, -remanejo.total))
                                 FROM ctbcontadespesaextra remanejo
                                      INNER JOIN ctbcontadespesa conta
                                         ON remanejo.iddespesa = conta.id AND remanejo.idcliente = conta.idcliente
                                WHERE remanejo.idcliente = movimento.idcliente
                                  AND conta.exercicio = :exe3 - 1
                                GROUP BY remanejo.idcliente), 0.00) AS valor_remanejado_anterior,
                           IFNULL(
                              (SELECT SUM(IF(remanejo.operacao = 'S', remanejo.total, -remanejo.total))
                                 FROM ctbcontadespesaextra remanejo
                                      INNER JOIN ctbcontadespesa conta
                                         ON remanejo.iddespesa = conta.id AND remanejo.idcliente = conta.idcliente
                                WHERE remanejo.idcliente = movimento.idcliente
                                  AND conta.exercicio = :exe4
                                GROUP BY remanejo.idcliente), 0.00) AS valor_remanejado_exercicio,
                           SUM(IF(empenho.exercicio = :exe5 - 1, movimento.emissao - movimento.anular, 0.00)) AS valor_empenhado_anterior,
                           SUM(IF(empenho.exercicio = :exe6, movimento.emissao - movimento.anular, 0.00)) AS valor_empenhado_exercicio,
                           SUM(IF(empenho.exercicio = :exe7 - 1, movimento.pagamento, 0.00)) AS valor_pago_anterior,
                           SUM(IF(empenho.exercicio = :exe8, movimento.pagamento, 0.00)) AS valor_pago_exercicio
                      FROM ctbempenhomovimento movimento
                           INNER JOIN ctbempenho empenho
                              ON movimento.idempenho = empenho.id AND movimento.idcliente = empenho.idcliente
                           INNER JOIN ctbcontadespesa despesa
                              ON empenho.iddespesa = despesa.id AND empenho.idcliente = despesa.idcliente
                           INNER JOIN ctbunidadeorcamentaria unidade
                              ON despesa.idunidadeorcamentaria = unidade.id AND despesa.idcliente = unidade.idcliente
                           INNER JOIN ctborgao orgao
                              ON unidade.idorgao = orgao.id AND unidade.idcliente = orgao.idcliente
                     WHERE movimento.idcliente = :id
                       AND empenho.exercicio IN (:exe9 - 1, :exe10)
                     GROUP BY movimento.idcliente) query
        ";

        return DB::selectOne($sql, [
            'id' => $idcliente,
            'exe1' => $exercicio,
            'exe2' => $exercicio,
            'exe3' => $exercicio,
            'exe4' => $exercicio,
            'exe5' => $exercicio,
            'exe6' => $exercicio,
            'exe7' => $exercicio,
            'exe8' => $exercicio,
            'exe9' => $exercicio,
            'exe10' => $exercicio
        ]);
    }

    /**
     * Retorna a evolução mensalizada das despesas do exercício e anterior
     */
    public function getEvolucaoMensalDespesa($idcliente, $exercicio)
    {
        $sql = "
            SELECT movimento.idcliente AS idcliente,
                   movimento.mes AS mes,
                   SUM(IF(empenho.exercicio = :exe1 - 1, movimento.emissao - movimento.anular, 0.00)) AS valor_empenhado_anterior,
                   SUM(IF(empenho.exercicio = :exe2, movimento.emissao - movimento.anular, 0.00)) AS valor_empenhado_exercicio,
                   SUM(IF(empenho.exercicio = :exe3 - 1, movimento.pagamento, 0.00)) AS valor_pago_anterior,
                   SUM(IF(empenho.exercicio = :exe4, movimento.pagamento, 0.00)) AS valor_pago_exercicio
              FROM ctbempenhomovimento movimento
                   INNER JOIN ctbempenho empenho
                      ON movimento.idempenho = empenho.id AND movimento.idcliente = empenho.idcliente
                   INNER JOIN ctbcontadespesa despesa
                      ON empenho.iddespesa = despesa.id AND empenho.idcliente = despesa.idcliente
                   INNER JOIN ctbunidadeorcamentaria unidade
                      ON despesa.idunidadeorcamentaria = unidade.id AND despesa.idcliente = unidade.idcliente
                   INNER JOIN ctborgao orgao
                      ON unidade.idorgao = orgao.id AND unidade.idcliente = orgao.idcliente
             WHERE movimento.idcliente = :id
               AND empenho.exercicio IN (:exe5 - 1, :exe6)
             GROUP BY movimento.idcliente, movimento.mes
             ORDER BY movimento.idcliente, movimento.mes
        ";

        return DB::select($sql, [
            'id' => $idcliente,
            'exe1' => $exercicio,
            'exe2' => $exercicio,
            'exe3' => $exercicio,
            'exe4' => $exercicio,
            'exe5' => $exercicio,
            'exe6' => $exercicio
        ]);
    }
}
