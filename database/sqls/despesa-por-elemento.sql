/*
 Totalizar o valor do elemento para o seu superior (Até não "superior_id" ser igual a NULL).
 Remover os níveis diferentes de "2".
 */
-- https://sistemas.hardsoftsfa.com.br/transparencia/cacequipm/planejamento/loa/despesa/elemento/
SELECT
    elemento.id AS id,
    elemento.idsuperior AS superior_id,
    elemento.estrutural AS estrutural,
    elemento.nome AS descricao,
    elemento.nivel AS nivel,
    IFNULL(
        (
            SELECT
                SUM(orcamento.total)
            FROM
                ctbcontadespesaloa orcamento
                INNER JOIN ctbcontadespesa contaDespesa ON contaDespesa.id = orcamento.iddespesa
                AND contaDespesa.idcliente = orcamento.idcliente
            WHERE
                contaDespesa.idelemento = elemento.id
            GROUP BY
                contaDespesa.idelemento
        ),
        0.00
    ) AS valor_orcado
FROM
    ctbelemento elemento
WHERE
    elemento.idcliente = :id
    AND elemento.exercicio = :exc
    AND elemento.tipo = 'D'
HAVING
    valor_orcado > 0
ORDER BY
    estrutural