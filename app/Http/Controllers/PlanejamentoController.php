<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlanejamentoController extends Controller
{
    /**
     * Exibe a página de detalhamento por Elemento de Despesa
     */
    public function despesaPorElemento()
    {
        // Suposição de valores (você pode pegar do request ou da sessão)
        $idCliente = env('CLIENT_ID');

        $resumoAnual = DB::table('ctbcontadespesa as despesa')
            ->select([
                'despesa.idcliente as cliente_id',
                'despesa.exercicio as exercicio',
            ])
            ->selectRaw("
            SUM((
                SELECT SUM(orcamento.total)
                FROM ctbcontadespesaloa orcamento
                WHERE orcamento.iddespesa = despesa.id
                  AND orcamento.idcliente = despesa.idcliente
                GROUP BY orcamento.iddespesa
            )) AS valor_orcado
        ")
            ->selectRaw("
            SUM((
                SELECT SUM(IF(decreto.operacao = 'S', decreto.total, -decreto.total))
                FROM ctbcontadespesaextra decreto
                WHERE decreto.iddespesa = despesa.id
                  AND decreto.idcliente = despesa.idcliente
                GROUP BY decreto.iddespesa
            )) AS valor_corrigido
        ")
            ->selectRaw("
            SUM((
                SELECT SUM(movimento.emissao - movimento.anular)
                FROM ctbempenhomovimento movimento
                INNER JOIN ctbempenho empenho
                   ON empenho.id = movimento.idempenho
                  AND empenho.idcliente = movimento.idcliente
                WHERE empenho.iddespesa = despesa.id
                  AND movimento.idcliente = despesa.idcliente
                  AND empenho.tipo = 'O'
                GROUP BY empenho.iddespesa
            )) AS valor_executado
        ")
            ->selectRaw("
            SUM((
                SELECT SUM(movimento.emissao - movimento.anular)
                FROM ctbempenhomovimento movimento
                INNER JOIN ctbempenho empenho
                   ON empenho.id = movimento.idempenho
                  AND empenho.idcliente = movimento.idcliente
                WHERE empenho.iddespesa = despesa.id
                  AND movimento.idcliente = despesa.idcliente
                  AND empenho.tipo = 'R'
                GROUP BY empenho.iddespesa
            )) AS valor_restos
        ")
            ->where('despesa.idcliente', $idCliente)
            ->groupBy('despesa.idcliente', 'despesa.exercicio')
            ->orderByDesc('despesa.exercicio')
            ->get();

        return view('planejamento.loa.despesa.elemento', compact('resumoAnual'));
    }

    public function despesaPorElementoDetalhePorExercicio($exercicio)
    {
        $idCliente = env('CLIENT_ID');

        // 1. Definimos a query base (a que você já tinha)
        $subQuery = DB::table('ctbelemento as elemento')
            ->select([
                'elemento.id as id',
                'elemento.idsuperior as superior_id',
                'elemento.estrutural as estrutural',
                'elemento.nome as descricao',
                'elemento.nivel as nivel'
            ])
            ->selectRaw("
                        IFNULL(
                            (SELECT SUM(orcamento.total)
                            FROM ctbcontadespesaloa orcamento
                            INNER JOIN ctbcontadespesa contaDespesa
                                ON contaDespesa.id = orcamento.iddespesa
                                AND contaDespesa.idcliente = orcamento.idcliente
                            WHERE contaDespesa.idelemento = elemento.id
                            GROUP BY contaDespesa.idelemento), 0.00
                        ) AS valor_orcado
                    ")
            ->where('elemento.idcliente', $idCliente)
            ->where('elemento.exercicio', $exercicio)
            ->where('elemento.tipo', 'D');


        // Query externa com Paginação
        $data = DB::table(DB::raw("({$subQuery->toSql()}) as tabela_calculada"))
            ->mergeBindings($subQuery)
            ->where('valor_orcado', '>', 0)
            ->orderBy('estrutural')
            ->paginate(25);

        return view('planejamento.loa.despesa.detalhe', compact('data', 'exercicio'));
    }
}
