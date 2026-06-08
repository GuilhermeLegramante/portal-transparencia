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

   /**
    * Obtém o resumo de despesas mensalizado por Unidade Orçamentária
    */
   public function getResumoUnidades(int $idCliente, int $exercicio): array
   {
      // CORRIGIDO: Vinculação de parâmetros simplificada usando :exercicioAtual e validação direta na coluna empenho.exercicio
      $sql = "SELECT CONCAT(orgao.codigo, '.', unidade.codigo) AS codigo, unidade.nome AS descricao, movimento.mes AS mes, SUM(IF(empenho.exercicio = :exercicioAtual - 1, movimento.emissao - movimento.anular, 0.00)) AS valor_empenhado_anterior, SUM(IF(empenho.exercicio = :exercicioAtual, movimento.emissao - movimento.anular, 0.00)) AS valor_empenhado_exercicio, SUM(IF(empenho.exercicio = :exercicioAtual - 1, movimento.pagamento, 0.00)) AS valor_pago_anterior, SUM(IF(empenho.exercicio = :exercicioAtual, movimento.pagamento, 0.00)) AS valor_pago_exercicio FROM ctbempenhomovimento movimento INNER JOIN ctbempenho empenho ON movimento.idempenho = empenho.id AND movimento.idcliente = empenho.idcliente INNER JOIN ctbcontadespesa despesa ON empenho.iddespesa = despesa.id AND empenho.idcliente = despesa.idcliente INNER JOIN ctbunidadeorcamentaria unidade ON despesa.idunidadeorcamentaria = unidade.id AND despesa.idcliente = unidade.idcliente INNER JOIN ctborgao orgao ON unidade.idorgao = orgao.id AND unidade.idcliente = orgao.idcliente WHERE movimento.idcliente = :id AND empenho.exercicio IN (:exercicioAnterior, :exercicioAtualRepetido) GROUP BY orgao.codigo, unidade.codigo, unidade.nome, movimento.mes ORDER BY CAST(orgao.codigo AS UNSIGNED), CAST(unidade.codigo AS UNSIGNED), mes";

      return DB::select($sql, [
         'id'                       => $idCliente,
         'exercicioAtual'           => $exercicio,
         'exercicioAnterior'        => $exercicio - 1,
         'exercicioAtualRepetido'   => $exercicio
      ]);
   }

   /**
    * Obtém o resumo de despesas mensalizado por Função
    */
   public function getResumoFuncoes(int $idCliente, int $exercicio): array
   {
      $sql = "SELECT funcao.codigo AS codigo, funcao.nome AS descricao, movimento.mes AS mes, SUM(IF(empenho.exercicio = :exercicioAtual - 1, movimento.emissao - movimento.anular, 0.00)) AS valor_emissao_anterior, SUM(IF(empenho.exercicio = :exercicioAtual, movimento.emissao - movimento.anular, 0.00)) AS valor_emissao_exercicio, SUM(IF(empenho.exercicio = :exercicioAtual - 1, movimento.pagamento, 0.00)) AS valor_pago_anterior, SUM(IF(empenho.exercicio = :exercicioAtual, movimento.pagamento, 0.00)) AS valor_pago_exercicio FROM ctbempenhomovimento movimento INNER JOIN ctbempenho empenho ON movimento.idempenho = empenho.id AND movimento.idcliente = empenho.idcliente INNER JOIN ctbcontadespesa despesa ON empenho.iddespesa = despesa.id AND empenho.idcliente = despesa.idcliente INNER JOIN ctbfuncao funcao ON despesa.idfuncao = funcao.id AND despesa.idcliente = funcao.idcliente WHERE movimento.idcliente = :id AND empenho.exercicio IN (:exercicioAnterior, :exercicioAtualRepetido) GROUP BY funcao.codigo, funcao.nome, movimento.mes ORDER BY descricao, mes";

      return DB::select($sql, [
         'id'                       => $idCliente,
         'exercicioAtual'           => $exercicio,
         'exercicioAnterior'        => $exercicio - 1,
         'exercicioAtualRepetido'   => $exercicio
      ]);
   }

   /**
    * Obtém o resumo de despesas mensalizado por Subfunção
    */
   public function getResumoSubfuncoes(int $idCliente, int $exercicio): array
   {
      $sql = "SELECT subfuncao.codigo AS codigo, subfuncao.nome AS descricao, movimento.mes AS mes, SUM(IF(empenho.exercicio = :exercicioAtual - 1, movimento.emissao - movimento.anular, 0.00)) AS valor_emissao_anterior, SUM(IF(empenho.exercicio = :exercicioAtual, movimento.emissao - movimento.anular, 0.00)) AS valor_emissao_exercicio, SUM(IF(empenho.exercicio = :exercicioAtual - 1, movimento.pagamento, 0.00)) AS valor_pago_anterior, SUM(IF(empenho.exercicio = :exercicioAtual, movimento.pagamento, 0.00)) AS valor_pago_exercicio FROM ctbempenhomovimento movimento INNER JOIN ctbempenho empenho ON movimento.idempenho = empenho.id AND movimento.idcliente = empenho.idcliente INNER JOIN ctbcontadespesa despesa ON empenho.iddespesa = despesa.id AND empenho.idcliente = despesa.idcliente INNER JOIN ctbsubfuncao subfuncao ON despesa.idsubfuncao = subfuncao.id AND despesa.idcliente = subfuncao.idcliente WHERE movimento.idcliente = :id AND empenho.exercicio IN (:exercicioAnterior, :exercicioAtualRepetido) GROUP BY subfuncao.codigo, subfuncao.nome, movimento.mes ORDER BY descricao, mes";

      return DB::select($sql, [
         'id'                       => $idCliente,
         'exercicioAtual'           => $exercicio,
         'exercicioAnterior'        => $exercicio - 1,
         'exercicioAtualRepetido'   => $exercicio
      ]);
   }

   /**
    * Obtém o resumo de despesas mensalizado por Elemento de Despesa
    */
   public function getResumoElementos(int $idCliente, int $exercicio): array
   {
      $sql = "SELECT elemento.estrutural AS estrutural, elemento.nome AS descricao, movimento.mes AS mes, SUM(IF(empenho.exercicio = :exercicioAtual - 1, movimento.emissao - movimento.anular, 0.00)) AS valor_emissao_anterior, SUM(IF(empenho.exercicio = :exercicioAtual, movimento.emissao - movimento.anular, 0.00)) AS valor_emissao_exercicio, SUM(IF(empenho.exercicio = :exercicioAtual - 1, movimento.pagamento, 0.00)) AS valor_pago_anterior, SUM(IF(empenho.exercicio = :exercicioAtual, movimento.pagamento, 0.00)) AS valor_pago_exercicio FROM ctbempenhomovimento movimento INNER JOIN ctbempenho empenho ON movimento.idempenho = empenho.id AND movimento.idcliente = empenho.idcliente INNER JOIN ctbcontadespesa despesa ON empenho.iddespesa = despesa.id AND empenho.idcliente = despesa.idcliente INNER JOIN ctbelemento elemento ON despesa.idelemento = elemento.id AND despesa.idcliente = elemento.idcliente WHERE movimento.idcliente = :id AND empenho.exercicio IN (:exercicioAnterior, :exercicioAtualRepetido) GROUP BY elemento.estrutural, elemento.nome, movimento.mes ORDER BY estrutural, mes";

      return DB::select($sql, [
         'id'                       => $idCliente,
         'exercicioAtual'           => $exercicio,
         'exercicioAnterior'        => $exercicio - 1,
         'exercicioAtualRepetido'   => $exercicio
      ]);
   }

   /**
    * Obtém o resumo de despesas mensalizado por Recurso Vinculado
    */
   public function getResumoRecursos(int $idCliente, int $exercicio): array
   {
      $sql = "SELECT recurso.codigo AS codigo, recurso.nome AS descricao, movimento.mes AS mes, SUM(IF(empenho.exercicio = :exercicioAtual - 1, movimento.emissao - movimento.anular, 0.00)) AS valor_emissao_anterior, SUM(IF(empenho.exercicio = :exercicioAtual, movimento.emissao - movimento.anular, 0.00)) AS valor_emissao_exercicio, SUM(IF(empenho.exercicio = :exercicioAtual - 1, movimento.pagamento, 0.00)) AS valor_pago_anterior, SUM(IF(empenho.exercicio = :exercicioAtual, movimento.pagamento, 0.00)) AS valor_pago_exercicio FROM ctbempenhomovimento movimento INNER JOIN ctbempenho empenho ON movimento.idempenho = empenho.id AND movimento.idcliente = empenho.idcliente INNER JOIN ctbrecursovinculado recurso ON empenho.idrecurso = recurso.id AND empenho.idcliente = recurso.idcliente WHERE movimiento.idcliente = :id AND empenho.exercicio IN (:exercicioAnterior, :exercicioAtualRepetido) GROUP BY recurso.codigo, recurso.nome, movimento.mes ORDER BY codigo, mes";

      return DB::select($sql, [
         'id'                       => $idCliente,
         'exercicioAtual'           => $exercicio,
         'exercicioAnterior'        => $exercicio - 1,
         'exercicioAtualRepetido'   => $exercicio
      ]);
   }
}
