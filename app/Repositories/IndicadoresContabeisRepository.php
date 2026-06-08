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
      $sql = "SELECT 
                    CONCAT(orgao.codigo, '.', unidade.codigo) AS codigo, 
                    unidade.nome AS descricao, 
                    movimento.mes AS mes, 
                    SUM(IF(empenho.exercicio = ? - 1, movimento.emissao - movimento.anular, 0.00)) AS valor_empenhado_anterior, 
                    SUM(IF(empenho.exercicio = ?, movimento.emissao - movimento.anular, 0.00)) AS valor_empenhado_exercicio, 
                    SUM(IF(empenho.exercicio = ? - 1, movimento.pagamento, 0.00)) AS valor_pago_anterior, 
                    SUM(IF(empenho.exercicio = ?, movimento.pagamento, 0.00)) AS valor_pago_exercicio 
                FROM ctbempenhomovimento movimento 
                INNER JOIN ctbempenho empenho 
                    ON movimento.idempenho = empenho.id AND movimento.idcliente = empenho.idcliente 
                INNER JOIN ctbcontadespesa despesa 
                    ON empenho.iddespesa = despesa.id AND empenho.idcliente = despesa.idcliente 
                INNER JOIN ctbunidadeorcamentaria unidade 
                    ON despesa.idunidadeorcamentaria = unidade.id AND despesa.idcliente = unidade.idcliente 
                INNER JOIN ctborgao orgao 
                    ON unidade.idorgao = orgao.id AND unidade.idcliente = orgao.idcliente 
                WHERE movimento.idcliente = ? 
                  AND empenho.exercicio IN (? - 1, ?) 
                GROUP BY orgao.codigo, unidade.codigo, unidade.nome, movimento.mes 
                ORDER BY CAST(orgao.codigo AS UNSIGNED), CAST(unidade.codigo AS UNSIGNED), mes";

      return DB::select($sql, [
         $exercicio, // valor_empenhado_anterior
         $exercicio, // valor_empenhado_exercicio
         $exercicio, // valor_pago_anterior
         $exercicio, // valor_pago_exercicio
         $idCliente, // idcliente
         $exercicio, // IN (? - 1,
         $exercicio  // ?)
      ]);
   }

   /**
    * Obtém o resumo de despesas mensalizado por Função
    */
   public function getResumoFuncoes(int $idCliente, int $exercicio): array
   {
      $sql = "SELECT 
                    funcao.codigo AS codigo, 
                    funcao.nome AS descricao, 
                    movimento.mes AS mes, 
                    SUM(IF(empenho.exercicio = ? - 1, movimento.emissao - movimento.anular, 0.00)) AS valor_emissao_anterior, 
                    SUM(IF(empenho.exercicio = ?, movimento.emissao - movimento.anular, 0.00)) AS valor_emissao_exercicio, 
                    SUM(IF(empenho.exercicio = ? - 1, movimento.pagamento, 0.00)) AS valor_pago_anterior, 
                    SUM(IF(empenho.exercicio = ?, movimento.pagamento, 0.00)) AS valor_pago_exercicio 
                FROM ctbempenhomovimento movimento 
                INNER JOIN ctbempenho empenho 
                    ON movimento.idempenho = empenho.id AND movimento.idcliente = empenho.idcliente 
                INNER JOIN ctbcontadespesa despesa 
                    ON empenho.iddespesa = despesa.id AND empenho.idcliente = despesa.idcliente 
                INNER JOIN ctbfuncao funcao 
                    ON despesa.idfuncao = funcao.id AND despesa.idcliente = funcao.idcliente 
                WHERE movimento.idcliente = ? 
                  AND empenho.exercicio IN (? - 1, ?) 
                GROUP BY funcao.codigo, funcao.nome, movimento.mes 
                ORDER BY descricao, mes";

      return DB::select($sql, [$exercicio, $exercicio, $exercicio, $exercicio, $idCliente, $exercicio, $exercicio]);
   }

   /**
    * Obtém o resumo de despesas mensalizado por Subfunção
    */
   public function getResumoSubfuncoes(int $idCliente, int $exercicio): array
   {
      $sql = "SELECT 
                    subfuncao.codigo AS codigo, 
                    subfuncao.nome AS descricao, 
                    movimento.mes AS mes, 
                    SUM(IF(empenho.exercicio = ? - 1, movimento.emissao - movimento.anular, 0.00)) AS valor_emissao_anterior, 
                    SUM(IF(empenho.exercicio = ?, movimento.emissao - movimento.anular, 0.00)) AS valor_emissao_exercicio, 
                    SUM(IF(empenho.exercicio = ? - 1, movimento.pagamento, 0.00)) AS valor_pago_anterior, 
                    SUM(IF(empenho.exercicio = ?, movimento.pagamento, 0.00)) AS valor_pago_exercicio 
                FROM ctbempenhomovimento movimento 
                INNER JOIN ctbempenho empenho 
                    ON movimento.idempenho = empenho.id AND movimento.idcliente = empenho.idcliente 
                INNER JOIN ctbcontadespesa despesa 
                    ON empenho.iddespesa = despesa.id AND empenho.idcliente = despesa.idcliente 
                INNER JOIN ctbsubfuncao subfuncao 
                    ON despesa.idsubfuncao = subfuncao.id AND despesa.idcliente = subfuncao.idcliente 
                WHERE movimento.idcliente = ? 
                  AND empenho.exercicio IN (? - 1, ?) 
                GROUP BY subfuncao.codigo, subfuncao.nome, movimento.mes 
                ORDER BY descricao, mes";

      return DB::select($sql, [$exercicio, $exercicio, $exercicio, $exercicio, $idCliente, $exercicio, $exercicio]);
   }

   /**
    * Obtém o resumo de despesas mensalizado por Elemento de Despesa
    */
   public function getResumoElementos(int $idCliente, int $exercicio): array
   {
      $sql = "SELECT 
                    elemento.estrutural AS estrutural, 
                    elemento.nome AS descricao, 
                    movimento.mes AS mes, 
                    SUM(IF(empenho.exercicio = ? - 1, movimento.emissao - movimento.anular, 0.00)) AS valor_emissao_anterior, 
                    SUM(IF(empenho.exercicio = ?, movimento.emissao - movimento.anular, 0.00)) AS valor_emissao_exercicio, 
                    SUM(IF(empenho.exercicio = ? - 1, movimento.pagamento, 0.00)) AS valor_pago_anterior, 
                    SUM(IF(empenho.exercicio = ?, movimento.pagamento, 0.00)) AS valor_pago_exercicio 
                FROM ctbempenhomovimento movimento 
                INNER JOIN ctbempenho empenho 
                    ON movimento.idempenho = empenho.id AND movimento.idcliente = empenho.idcliente 
                INNER JOIN ctbcontadespesa despesa 
                    ON empenho.iddespesa = despesa.id AND empenho.idcliente = despesa.idcliente 
                INNER JOIN ctbelemento elemento 
                    ON despesa.idelemento = elemento.id AND despesa.idcliente = elemento.idcliente 
                WHERE movimento.idcliente = ? 
                  AND empenho.exercicio IN (? - 1, ?) 
                GROUP BY elemento.estrutural, elemento.nome, movimento.mes 
                ORDER BY estrutural, mes";

      return DB::select($sql, [$exercicio, $exercicio, $exercicio, $exercicio, $idCliente, $exercicio, $exercicio]);
   }

   /**
    * Obtém o resumo de despesas mensalizado por Recurso Vinculado
    */
   public function getResumoRecursos(int $idCliente, int $exercicio): array
   {
      $sql = "SELECT 
                    recurso.codigo AS codigo, 
                    recurso.nome AS descricao, 
                    movimento.mes AS mes, 
                    SUM(IF(empenho.exercicio = ? - 1, movimento.emissao - movimento.anular, 0.00)) AS valor_emissao_anterior, 
                    SUM(IF(empenho.exercicio = ?, movimento.emissao - movimento.anular, 0.00)) AS valor_emissao_exercicio, 
                    SUM(IF(empenho.exercicio = ? - 1, movimento.pagamento, 0.00)) AS valor_pago_anterior, 
                    SUM(IF(empenho.exercicio = ?, movimento.pagamento, 0.00)) AS valor_pago_exercicio 
                FROM ctbempenhomovimento movimento 
                INNER JOIN ctbempenho empenho 
                    ON movimento.idempenho = empenho.id AND movimento.idcliente = empenho.idcliente 
                INNER JOIN ctbrecursovinculado recurso 
                    ON empenho.idrecurso = recurso.id AND empenho.idcliente = recurso.idcliente 
                WHERE movimento.idcliente = ? 
                  AND empenho.exercicio IN (? - 1, ?) 
                GROUP BY recurso.codigo, recurso.nome, movimento.mes 
                ORDER BY codigo, mes";

      return DB::select($sql, [$exercicio, $exercicio, $exercicio, $exercicio, $idCliente, $exercicio, $exercicio]);
   }

   /**
    * Obtém o resumo de despesas consolidado por unidade orçamentária.
    * * @param int $idCliente
    * @param int $exercicio
    * @return array
    */
   public function getResumoPorUnidade(int $idCliente, int $exercicio): array
   {
      // Utilizamos o select para executar a query com os parâmetros vinculados
      return DB::select("
        SELECT 
            CONCAT(orgao.codigo, '.', unidade.codigo) AS codigo,
            unidade.nome AS descricao,
            SUM(IF(empenho.exercicio = :ant, movimento.emissao - movimento.anular, 0.00)) AS valor_empenhado_anterior,
            SUM(IF(empenho.exercicio = :exe, movimento.emissao - movimento.anular, 0.00)) AS valor_empenhado_exercicio,
            SUM(IF(empenho.exercicio = :ant, movimento.pagamento, 0.00)) AS valor_pago_anterior,
            SUM(IF(empenho.exercicio = :exe, movimento.pagamento, 0.00)) AS valor_pago_exercicio
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
          AND empenho.exercicio IN (:ant, :exe)
        GROUP BY orgao.codigo, unidade.codigo, unidade.nome
        ORDER BY CAST(orgao.codigo AS UNSIGNED), CAST(unidade.codigo AS UNSIGNED)
    ", [
         'id'  => $idCliente,
         'ant' => $exercicio - 1,
         'exe' => $exercicio
      ]);
   }

   /**
    * Retorna o resumo consolidado por Recurso Vinculado com cálculo de variação.
    */
   public function getResumoPorRecurso(int $idCliente, int $exercicio): array
   {
      $anoAnterior = $exercicio - 1;

      return DB::select("
        SELECT query.*,
               IFNULL(((1 - (query.valor_empenhado_exercicio / NULLIF(query.valor_empenhado_anterior, 0))) * 100) * -1, 0.00) AS variacao_empenhado,
               IFNULL(((1 - (query.valor_pago_exercicio / NULLIF(query.valor_pago_anterior, 0))) * 100) * -1, 0.00) AS variacao_pago
        FROM (
            SELECT 
                recurso.codigo AS codigo,
                recurso.nome AS descricao,
                IFNULL(SUM(IF(empenho.exercicio = :ant, movimento.emissao - movimento.anular, 0.00)), 0.00) AS valor_empenhado_anterior,
                IFNULL(SUM(IF(empenho.exercicio = :exe, movimento.emissao - movimento.anular, 0.00)), 0.00) AS valor_empenhado_exercicio,
                IFNULL(SUM(IF(empenho.exercicio = :ant, movimento.pagamento, 0.00)), 0.00) AS valor_pago_anterior,
                IFNULL(SUM(IF(empenho.exercicio = :exe, movimento.pagamento, 0.00)), 0.00) AS valor_pago_exercicio
            FROM ctbempenhomovimento movimento
            INNER JOIN ctbempenho empenho 
                ON movimento.idempenho = empenho.id AND movimento.idcliente = empenho.idcliente
            INNER JOIN ctbrecursovinculado recurso 
                ON empenho.idrecurso = recurso.id AND empenho.idcliente = recurso.idcliente
            WHERE movimento.idcliente = :id
              AND empenho.exercicio IN (:ant, :exe)
            GROUP BY recurso.codigo, recurso.nome
        ) query
        ORDER BY query.codigo
    ", [
         'id'  => $idCliente,
         'ant' => $anoAnterior,
         'exe' => $exercicio
      ]);
   }

   /**
    * Retorna o resumo consolidado por Elemento de Despesa.
    */
   public function getResumoPorElemento(int $idCliente, int $exercicio): array
   {
      $anoAnterior = $exercicio - 1;

      return DB::select("
        SELECT 
            query.*,
            (query.empenhado_exercicio - query.empenhado_anterior) AS diferenca,
            IFNULL(((1 - (query.empenhado_exercicio / NULLIF(query.empenhado_anterior, 0))) * 100) * -1, 0.00) AS variacao
        FROM (
            SELECT 
                elemento.estrutural AS estrutural,
                elemento.nome AS descricao,
                SUM(IF(empenho.exercicio = :ant, movimento.emissao - movimento.anular, 0.00)) AS empenhado_anterior,
                SUM(IF(empenho.exercicio = :exe, movimento.emissao - movimento.anular, 0.00)) AS empenhado_exercicio,
                SUM(IF(empenho.exercicio = :ant, movimento.pagamento, 0.00)) AS pagamento_anterior,
                SUM(IF(empenho.exercicio = :exe, movimento.pagamento, 0.00)) AS pagamento_exercicio
            FROM ctbempenhomovimento movimento
            INNER JOIN ctbempenho empenho 
                ON empenho.id = movimento.idempenho AND empenho.idcliente = movimento.idcliente
            INNER JOIN ctbcontadespesa despesa 
                ON despesa.id = empenho.iddespesa AND despesa.idcliente = despesa.idcliente
            INNER JOIN ctbelemento elemento 
                ON elemento.id = despesa.idelemento AND elemento.idcliente = despesa.idcliente
            WHERE movimento.idcliente = :id
              AND empenho.exercicio IN (:ant, :exe)
            GROUP BY elemento.estrutural, elemento.nome
        ) query
        ORDER BY query.estrutural
    ", [
         'id'  => $idCliente,
         'ant' => $anoAnterior,
         'exe' => $exercicio
      ]);
   }

   /**
    * Retorna o resumo consolidado por Subfunção com cálculos de variação.
    */
   public function getResumoPorSubfuncao(int $idCliente, int $exercicio): array
   {
      $anoAnterior = $exercicio - 1;

      return DB::select("
        SELECT 
            query.codigo,
            query.descricao,
            SUM(query.valor_orcado_anterior + query.valor_remanejo_anterior) AS valor_atualizado_anterior,
            SUM(query.valor_orcado_exercicio + query.valor_remanejo_exercicio) AS valor_atualizado_exercicio,
            IFNULL(((1 - (SUM(query.valor_orcado_exercicio + query.valor_remanejo_exercicio) / 
                NULLIF(SUM(query.valor_orcado_anterior + query.valor_remanejo_anterior), 0))) * 100) * -1, 0.00) AS variacao_atualizado,
            SUM(query.valor_empenhado_anterior) AS valor_empenhado_anterior,
            SUM(query.valor_empenhado_exercicio) AS valor_empenhado_exercicio,
            IFNULL(((1 - (SUM(query.valor_empenhado_exercicio) / 
                NULLIF(SUM(query.valor_empenhado_anterior), 0))) * 100) * -1, 0.00) AS variacao_gastos,
            SUM(query.valor_pago_anterior) AS valor_pago_anterior,
            SUM(query.valor_pago_exercicio) AS valor_pago_exercicio
        FROM (
            SELECT 
                subfuncao.codigo AS codigo,
                subfuncao.nome AS descricao,
                /* Cálculos baseados em subqueries para loa, movimento e remanejo conforme sua estrutura */
                (SELECT IFNULL(SUM(loa.total), 0.00) FROM ctbcontadespesaloa loa WHERE loa.iddespesa = despesa.id AND loa.exercicio = :exe) AS valor_orcado_exercicio,
                (SELECT IFNULL(SUM(loa.total), 0.00) FROM ctbcontadespesaloa loa WHERE loa.iddespesa = despesa.id AND loa.exercicio = :ant) AS valor_orcado_anterior,
                (SELECT IFNULL(SUM(mov.emissao - mov.anular), 0.00) FROM ctbempenhomovimento mov JOIN ctbempenho emp ON emp.id = mov.idempenho WHERE emp.iddespesa = despesa.id AND emp.exercicio = :exe) AS valor_empenhado_exercicio,
                (SELECT IFNULL(SUM(mov.emissao - mov.anular), 0.00) FROM ctbempenhomovimento mov JOIN ctbempenho emp ON emp.id = mov.idempenho WHERE emp.iddespesa = despesa.id AND emp.exercicio = :ant) AS valor_empenhado_anterior,
                (SELECT IFNULL(SUM(mov.pagamento), 0.00) FROM ctbempenhomovimento mov JOIN ctbempenho emp ON emp.id = mov.idempenho WHERE emp.iddespesa = despesa.id AND emp.exercicio = :exe) AS valor_pago_exercicio,
                (SELECT IFNULL(SUM(mov.pagamento), 0.00) FROM ctbempenhomovimento mov JOIN ctbempenho emp ON emp.id = mov.idempenho WHERE emp.iddespesa = despesa.id AND emp.exercicio = :ant) AS valor_pago_anterior,
                (SELECT IFNULL(SUM(IF(rem.operacao = 'S', rem.total, -rem.total)), 0.00) FROM ctbcontadespesaextra rem WHERE rem.iddespesa = despesa.id AND rem.exercicio = :exe) AS valor_remanejo_exercicio,
                (SELECT IFNULL(SUM(IF(rem.operacao = 'S', rem.total, -rem.total)), 0.00) FROM ctbcontadespesaextra rem WHERE rem.iddespesa = despesa.id AND rem.exercicio = :ant) AS valor_remanejo_anterior
            FROM ctbcontadespesa despesa
            INNER JOIN ctbsubfuncao subfuncao ON subfuncao.id = despesa.idsubfuncao
            WHERE despesa.idcliente = :id
        ) query
        GROUP BY query.codigo, query.descricao
        ORDER BY CAST(query.codigo AS UNSIGNED)
    ", [
         'id'  => $idCliente,
         'ant' => $anoAnterior,
         'exe' => $exercicio
      ]);
   }

   /**
    * Retorna o resumo consolidado por Função Orçamentária.
    */
   public function getResumoPorFuncao(int $idCliente, int $exercicio): array
   {
      $anoAnterior = $exercicio - 1;

      return DB::select("
        SELECT 
            query.codigo,
            query.descricao,
            SUM(query.valor_orcado_anterior + query.valor_remanejo_anterior) AS valor_atualizado_anterior,
            SUM(query.valor_orcado_exercicio + query.valor_remanejo_exercicio) AS valor_atualizado_exercicio,
            IFNULL(((1 - (SUM(query.valor_orcado_exercicio + query.valor_remanejo_exercicio) / 
                NULLIF(SUM(query.valor_orcado_anterior + query.valor_remanejo_anterior), 0))) * 100) * -1, 0.00) AS variacao_atualizado,
            SUM(query.valor_empenhado_anterior) AS valor_empenhado_anterior,
            SUM(query.valor_empenhado_exercicio) AS valor_empenhado_exercicio,
            IFNULL(((1 - (SUM(query.valor_empenhado_exercicio) / 
                NULLIF(SUM(query.valor_empenhado_anterior), 0))) * 100) * -1, 0.00) AS variacao_gastos,
            SUM(query.valor_pago_anterior) AS valor_pago_anterior,
            SUM(query.valor_pago_exercicio) AS valor_pago_exercicio
        FROM (
            SELECT 
                funcao.codigo AS codigo,
                funcao.nome AS descricao,
                (SELECT IFNULL(SUM(loa.total), 0) FROM ctbcontadespesaloa loa WHERE loa.iddespesa = despesa.id AND loa.exercicio = :exe) AS valor_orcado_exercicio,
                (SELECT IFNULL(SUM(loa.total), 0) FROM ctbcontadespesaloa loa WHERE loa.iddespesa = despesa.id AND loa.exercicio = :ant) AS valor_orcado_anterior,
                (SELECT IFNULL(SUM(mov.emissao - mov.anular), 0) FROM ctbempenhomovimento mov JOIN ctbempenho emp ON emp.id = mov.idempenho WHERE emp.iddespesa = despesa.id AND emp.exercicio = :exe) AS valor_empenhado_exercicio,
                (SELECT IFNULL(SUM(mov.emissao - mov.anular), 0) FROM ctbempenhomovimento mov JOIN ctbempenho emp ON emp.id = mov.idempenho WHERE emp.iddespesa = despesa.id AND emp.exercicio = :ant) AS valor_empenhado_anterior,
                (SELECT IFNULL(SUM(mov.pagamento), 0) FROM ctbempenhomovimento mov JOIN ctbempenho emp ON emp.id = mov.idempenho WHERE emp.iddespesa = despesa.id AND emp.exercicio = :exe) AS valor_pago_exercicio,
                (SELECT IFNULL(SUM(mov.pagamento), 0) FROM ctbempenhomovimento mov JOIN ctbempenho emp ON emp.id = mov.idempenho WHERE emp.iddespesa = despesa.id AND emp.exercicio = :ant) AS valor_pago_anterior,
                (SELECT IFNULL(SUM(IF(rem.operacao = 'S', rem.total, -rem.total)), 0) FROM ctbcontadespesaextra rem WHERE rem.iddespesa = despesa.id AND rem.exercicio = :exe) AS valor_remanejo_exercicio,
                (SELECT IFNULL(SUM(IF(rem.operacao = 'S', rem.total, -rem.total)), 0) FROM ctbcontadespesaextra rem WHERE rem.iddespesa = despesa.id AND rem.exercicio = :ant) AS valor_remanejo_anterior
            FROM ctbcontadespesa despesa
            INNER JOIN ctbfuncao funcao ON funcao.id = despesa.idfuncao
            WHERE despesa.idcliente = :id
        ) query
        GROUP BY query.codigo, query.descricao
        ORDER BY query.descricao
    ", [
         'id'  => $idCliente,
         'ant' => $anoAnterior,
         'exe' => $exercicio
      ]);
   }
}
