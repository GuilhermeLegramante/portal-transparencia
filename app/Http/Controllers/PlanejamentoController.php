<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlanejamentoController extends Controller
{
    /**
     * Exibe o resumo anual das despesas, agrupado por exercício.
     * Para cada exercício, calcula o valor orçado, corrigido, executado e restos a pagar.
     */
    public function resumoDespesa($filtro)
    {
        // Suposição de valores (você pode pegar do request ou da sessão)
        $idCliente = config('app.client_id');

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

        if ($filtro == 'orgao') {
            $breadcrumbTitle = 'Por Órgão';
        } else if ($filtro == 'elemento') {
            $breadcrumbTitle = 'Por Elemento';
        } else {
            $breadcrumbTitle = 'Por Recurso';
        }

        return view('planejamento.loa.despesa.index', compact('resumoAnual', 'filtro', 'breadcrumbTitle'));
    }

    /**
     * Exibe o detalhamento das despesas por elemento, para um exercício específico.
     * A consulta é otimizada para evitar N+1, utilizando subqueries e joins eficientes.
     */
    public function despesaPorElementoDetalhePorExercicio($exercicio)
    {
        $idCliente = config('app.client_id');

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


        // 1. Criamos a query base externa com todos os filtros (sem paginate e sem sum ainda)
        $queryBaseExterna = DB::table(DB::raw("({$subQuery->toSql()}) as tabela_calculada"))
            ->mergeBindings($subQuery)
            ->where('valor_orcado', '>', 0);

        // 2. Extraímos o Total Geral (Soma de todos os registros do banco)
        $totalGeralOrcado = $queryBaseExterna->sum('valor_orcado');

        // 3. Extraímos os dados paginados (Apenas os 25 desta página)
        $data = $queryBaseExterna
            ->orderBy('estrutural')
            // ->paginate(25); por causa do datatables vou trazer todos os dados e paginar no frontend
            ->get();

        return view('planejamento.loa.despesa.elemento.detalhe', compact('data', 'exercicio', 'totalGeralOrcado'));
    }

    /**
     * Exibe o detalhamento das despesas por órgão, para um exercício específico.
     * A consulta é otimizada para evitar N+1, utilizando subqueries e joins eficientes.
     */
    public function despesaPorOrgaoDetalhePorExercicio($exercicio)
    {
        $idCliente = config('app.client_id');

        // Query agrupada por Órgão
        $queryOrgao = DB::table('ctbcontadespesa as contaDespesa')
            ->join('ctbunidadeorcamentaria as unidade', function ($join) {
                $join->on('unidade.id', '=', 'contaDespesa.idunidadeorcamentaria')
                    ->on('unidade.idcliente', '=', 'contaDespesa.idcliente');
            })
            ->join('ctborgao as orgao', function ($join) {
                $join->on('orgao.id', '=', 'unidade.idorgao')
                    ->on('orgao.idcliente', '=', 'unidade.idcliente');
            })
            ->select([
                'orgao.id as orgao_id',
                'orgao.codigo as codigo',
                'orgao.nome as descricao',
            ])
            // Subquery para somar o orçamento de cada despesa do órgão
            ->selectRaw("
                            SUM((
                                SELECT SUM(orcamento.total)
                                FROM ctbcontadespesaloa orcamento
                                WHERE orcamento.idcliente = contaDespesa.idcliente
                                AND orcamento.iddespesa = contaDespesa.id
                            )) as valor_orcado
                        ")
            ->where('contaDespesa.idcliente', $idCliente)
            ->where('contaDespesa.exercicio', $exercicio)
            ->groupBy('orgao.id', 'orgao.codigo', 'orgao.nome')
            ->orderByRaw('CAST(orgao.codigo AS UNSIGNED) ASC');

        // 1. Definimos a query base externa (sem executar o paginate ainda)
        $queryBaseOrgao = DB::table(DB::raw("({$queryOrgao->toSql()}) as tab"))
            ->mergeBindings($queryOrgao)
            ->where('valor_orcado', '>', 0);

        // 2. Calculamos o TOTAL REAL (Soma de todos os órgãos do exercício)
        $totalGeralOrcado = $queryBaseOrgao->sum('valor_orcado');

        // 3. Agora aplicamos a ordenação e a paginação para a tabela
        $data = $queryBaseOrgao
            ->orderByRaw('CAST(codigo AS UNSIGNED) ASC')
            // ->paginate(25); por causa do datatables vou trazer todos os dados e paginar no frontend
            ->get();

        return view('planejamento.loa.despesa.orgao.detalhe', compact('data', 'exercicio', 'totalGeralOrcado'));
    }

    /**
     * Exibe o detalhamento das despesas por recurso, para um exercício específico.
     * A consulta é otimizada para evitar N+1, utilizando subqueries e joins eficientes.
     */
    public function despesaPorRecursoDetalhePorExercicio($exercicio)
    {
        $idCliente = config('app.client_id');

        // Query Base: Busca os recursos e soma o orçamento vinculado a cada um
        $subQueryRecurso = DB::table('ctbrecursovinculado as vinculo')
            ->select([
                'vinculo.id as recurso_id',
                'vinculo.codigo as codigo',
                'vinculo.nome as descricao',
            ])
            ->selectRaw("
            IFNULL(
                (SELECT SUM(orcamento.total)
                 FROM ctbcontadespesaloa orcamento
                 WHERE orcamento.idcliente = vinculo.idcliente
                   AND orcamento.idrecurso = vinculo.id
                 GROUP BY orcamento.idcliente, orcamento.idrecurso),
                0.00
            ) AS valor_orcado
        ")
            ->where('vinculo.idcliente', $idCliente)
            ->where('vinculo.exercicio', $exercicio);

        // 1. Prepara a Query Externa (sem executar ainda)
        $queryBaseExterna = DB::table(DB::raw("({$subQueryRecurso->toSql()}) as tab_recursos"))
            ->mergeBindings($subQueryRecurso)
            ->where('valor_orcado', '>', 0);

        // 2. Calcula o Total Geral de TODOS os registros do banco para este exercício
        $totalGeralOrcado = $queryBaseExterna->sum('valor_orcado');

        // 3. Agora sim, aplica ordenação e paginação para os dados da tabela
        $data = $queryBaseExterna
            ->orderBy('codigo')
            // ->paginate(25); por causa do datatables vou trazer todos os dados e paginar no frontend
            ->get();

        return view('planejamento.loa.despesa.recurso.detalhe', compact('data', 'exercicio', 'totalGeralOrcado'));
    }

    public function resumoReceita($filtro)
    {
        $idCliente = config('app.client_id');

        $resumoAnual = DB::table('ctbcontareceita as receita')
            ->select([
                'receita.idcliente as cliente_id',
                'receita.exercicio as exercicio',
            ])
            // Soma da LOA (Orçado)
            ->selectRaw("
                            SUM(
                                (SELECT SUM(orcamento.total)
                                FROM ctbcontareceitaloa orcamento
                                WHERE orcamento.idreceita = receita.id
                                AND orcamento.idcliente = receita.idcliente)
                            ) as valor_orcado
                        ")
            // Soma do Movimento (Arrecadado)
            ->selectRaw("
                            SUM(
                                (SELECT SUM(movimento.total)
                                FROM ctbcontareceitamovimento movimento
                                WHERE movimento.idreceita = receita.id
                                AND movimento.idcliente = receita.idcliente)
                            ) as valor_executado
                        ")
            ->where('receita.idcliente', $idCliente)
            ->groupBy('receita.idcliente', 'receita.exercicio')
            ->orderByDesc('receita.exercicio')
            ->get();

        if ($filtro == 'orgao') {
            $breadcrumbTitle = 'Por Órgão';
        } else if ($filtro == 'elemento') {
            $breadcrumbTitle = 'Por Elemento';
        } else {
            $breadcrumbTitle = 'Por Recurso';
        }

        return view('planejamento.loa.receita.index', compact('resumoAnual', 'filtro', 'breadcrumbTitle'));
    }

    /**
     * Exibe o detalhamento das receitass por elemento, para um exercício específico.
     * A consulta é otimizada para evitar N+1, utilizando subqueries e joins eficientes.
     */
    public function receitaPorElementoDetalhePorExercicio($exercicio)
    {
        $idCliente = config('app.client_id');

        // 1. Query Base: Agrupando por Elemento (Natureza)
        $subQuery = DB::table('ctbcontareceita as contaReceita')
            ->join('ctbelemento as elemento', function ($join) {
                $join->on('elemento.id', '=', 'contaReceita.idelemento')
                    ->on('elemento.idcliente', '=', 'contaReceita.idcliente');
            })
            ->select([
                'contaReceita.idcliente as cliente_id',
                'contaReceita.idelemento as elemento_id',
                'elemento.estrutural as estrutural',
                'elemento.nome as descricao',
            ])
            ->selectRaw("
            SUM((
                SELECT SUM(orcamento.total)
                FROM ctbcontareceitaloa orcamento
                WHERE orcamento.idcliente = contaReceita.idcliente
                  AND orcamento.idreceita = contaReceita.id
            )) as valor_orcado
        ")
            ->where('contaReceita.idcliente', $idCliente)
            ->where('contaReceita.exercicio', $exercicio)
            ->groupBy('contaReceita.idcliente', 'contaReceita.idelemento', 'elemento.estrutural', 'elemento.nome');

        // 2. Wrap Externo: Para totalizador e paginação correta
        $queryExterna = DB::table(DB::raw("({$subQuery->toSql()}) as tab_detalhe"))
            ->mergeBindings($subQuery)
            ->whereNotNull('valor_orcado');

        // Total Geral do Exercício (para o rodapé e cálculo de %)
        $totalGeralOrcado = $queryExterna->sum('valor_orcado');

        // Dados Paginados
        $data = $queryExterna->orderBy('estrutural')->paginate(25);

        return view('planejamento.loa.receita.elemento.detalhe', compact('data', 'exercicio', 'totalGeralOrcado'));
    }

    /**
     * Exibe o detalhamento das receitas por recurso, para um exercício específico.
     * A consulta é otimizada para evitar N+1, utilizando subqueries e joins eficientes.
     */
    public function receitaPorRecursoDetalhePorExercicio($exercicio)
    {
        $idCliente = config('app.client_id');

        // 1. Definimos a Subquery (Lógica interna do SQL)
        $subQuery = DB::table('ctbrecursovinculado as vinculo')
            ->select([
                'vinculo.id',
                'vinculo.codigo',
                'vinculo.nome as descricao',
                'vinculo.idcliente'
            ])
            ->selectRaw("
            IFNULL(
                (SELECT SUM(orcamento.total)
                 FROM ctbcontareceitaloa orcamento
                 WHERE orcamento.idcliente = vinculo.idcliente
                   AND orcamento.idrecurso = vinculo.id
                 GROUP BY orcamento.idcliente, orcamento.idrecurso),
                0.00
            ) AS valor_orcado
        ")
            ->where('vinculo.idcliente', $idCliente)
            ->where('vinculo.exercicio', $exercicio);

        // 2. Wrap Externo: Para totalizador e paginação correta
        $queryExterna = DB::table(DB::raw("({$subQuery->toSql()}) as tab_detalhe"))
            ->mergeBindings($subQuery)
            ->whereNotNull('valor_orcado')
            ->where('valor_orcado', '>', 0);

        // Total Geral do Exercício (para o rodapé e cálculo de %)
        $totalGeralOrcado = $queryExterna->sum('valor_orcado');

        // 3. Agora sim, aplica ordenação e paginação para os dados da tabela
        $data = $queryExterna
            ->orderBy('codigo')
            // ->paginate(25); por causa do datatables vou trazer todos os dados e paginar no frontend
            ->get();

        return view('planejamento.loa.receita.recurso.detalhe', compact('data', 'exercicio', 'totalGeralOrcado'));
    }
}
