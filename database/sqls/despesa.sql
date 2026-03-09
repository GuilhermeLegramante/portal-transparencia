/*
  Gastos previstos = valor_orcado + valor_corrigido
  Empenhos efetuados = valor_executado
  Percentual de comprometimento (%) = (valor_executado / valor_orcado) * 100
*/

SELECT despesa.idcliente AS cliente_id,
       despesa.exercicio AS exercicio,
      SUM(
        (SELECT SUM(orcamento.total)
           FROM ctbcontadespesaloa orcamento
          WHERE orcamento.iddespesa = despesa.id
            AND orcamento.idcliente = despesa.idcliente
          GROUP BY orcamento.iddespesa))
        AS valor_orcado,
      SUM(
        (SELECT SUM(IF(decreto.operacao = 'S', decreto.total, -decreto.total))
           FROM ctbcontadespesaextra decreto
          WHERE decreto.iddespesa = despesa.id
            AND decreto.idcliente = despesa.idcliente
          GROUP BY decreto.iddespesa))
        AS valor_corrigido,
      SUM(
        (SELECT SUM(movimento.emissao - movimento.anular)
           FROM ctbempenhomovimento movimento
                INNER JOIN ctbempenho empenho
                   ON     empenho.id = movimento.idempenho
                      AND empenho.idcliente = movimento.idcliente
          WHERE empenho.iddespesa = despesa.id
            AND movimento.idcliente = despesa.idcliente
            AND empenho.tipo = 'O'
          GROUP BY empenho.iddespesa))
        AS valor_executado,
      SUM(
        (SELECT SUM(movimento.emissao - movimento.anular)
           FROM ctbempenhomovimento movimento
                INNER JOIN ctbempenho empenho
                   ON     empenho.id = movimento.idempenho
                      AND empenho.idcliente = movimento.idcliente
          WHERE empenho.iddespesa = despesa.id
            AND movimento.idcliente = despesa.idcliente
            AND empenho.tipo = 'R'
          GROUP BY empenho.iddespesa))
        AS valor_restos
  FROM ctbcontadespesa despesa
 WHERE despesa.idcliente = :id
   AND despesa.exercicio = :exc
 GROUP BY despesa.idcliente, despesa.exercicio
 