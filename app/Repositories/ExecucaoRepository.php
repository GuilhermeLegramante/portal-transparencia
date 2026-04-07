<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class ExecucaoRepository
{
    public function getResumoGeralPorExercicio($idcliente)
    {
        // 1. Subquery Valor Orçado
        $subOrcado = DB::table('ctbcontadespesaloa as orcamento')
            ->selectRaw('SUM(orcamento.total)')
            ->whereColumn('orcamento.iddespesa', 'despesa.id')
            ->whereColumn('orcamento.idcliente', 'despesa.idcliente')
            ->groupBy('orcamento.iddespesa');

        // 2. Subquery Valor Corrigido
        $subCorrigido = DB::table('ctbcontadespesaextra as decreto')
            ->selectRaw("SUM(IF(decreto.operacao = 'S', decreto.total, -decreto.total))")
            ->whereColumn('decreto.iddespesa', 'despesa.id')
            ->whereColumn('decreto.idcliente', 'despesa.idcliente')
            ->groupBy('decreto.iddespesa');

        // 3. Subquery Valor Executado (Empenhos tipo 'O')
        $subExecutado = DB::table('ctbempenhomovimento as movimento')
            ->join('ctbempenho as empenho', function ($join) {
                $join->on('empenho.id', '=', 'movimento.idempenho')
                    ->on('empenho.idcliente', '=', 'movimento.idcliente');
            })
            ->selectRaw('SUM(movimento.emissao - movimento.anular)')
            ->whereColumn('empenho.iddespesa', 'despesa.id')
            ->whereColumn('movimento.idcliente', 'despesa.idcliente')
            ->where('empenho.tipo', 'O')
            ->groupBy('empenho.iddespesa');

        // 4. Subquery Valor Restos (Empenhos tipo 'R')
        $subRestos = DB::table('ctbempenhomovimento as movimento')
            ->join('ctbempenho as empenho', function ($join) {
                $join->on('empenho.id', '=', 'movimento.idempenho')
                    ->on('empenho.idcliente', '=', 'movimento.idcliente');
            })
            ->selectRaw('SUM(movimento.emissao - movimento.anular)')
            ->whereColumn('empenho.iddespesa', 'despesa.id')
            ->whereColumn('movimento.idcliente', 'despesa.idcliente')
            ->where('empenho.tipo', 'R')
            ->groupBy('empenho.iddespesa');

        // Query Principal - Sem filtro de exercício e com agrupamento por exercício
        return DB::table('ctbcontadespesa as despesa')
            ->select('despesa.exercicio') // Seleciona o ano para identificar a linha
            ->selectRaw("SUM(({$subOrcado->toSql()})) as valor_orcado")
            ->selectRaw("SUM(({$subCorrigido->toSql()})) as valor_corrigido")
            ->selectRaw("SUM(({$subExecutado->toSql()})) as valor_executado")
            ->selectRaw("SUM(({$subRestos->toSql()})) as valor_restos")
            ->mergeBindings($subOrcado)
            ->mergeBindings($subCorrigido)
            ->mergeBindings($subExecutado)
            ->mergeBindings($subRestos)
            ->where('despesa.idcliente', $idcliente)
            ->groupBy('despesa.exercicio') // Agrupa para gerar uma linha por ano
            ->orderBy('despesa.exercicio', 'desc') // Mais recente primeiro
            ->get();
    }

    public function getExecucaoPorElemento($exercicio, $idcliente)
    {
        // 1. Subqueries de Agregação (Idênticas às anteriores para manter consistência)
        $subOrcado = DB::table('ctbcontadespesaloa as orcamento')
            ->selectRaw('SUM(orcamento.total)')
            ->whereColumn('orcamento.iddespesa', 'despesa.id')
            ->whereColumn('orcamento.idcliente', 'despesa.idcliente')
            ->groupBy('orcamento.iddespesa');

        $subCorrigido = DB::table('ctbcontadespesaextra as decreto')
            ->selectRaw("SUM(IF(decreto.operacao = 'S', decreto.total, -decreto.total))")
            ->whereColumn('decreto.iddespesa', 'despesa.id')
            ->whereColumn('decreto.idcliente', 'despesa.idcliente')
            ->groupBy('decreto.iddespesa');

        $subExecutado = DB::table('ctbempenhomovimento as movimento')
            ->join('ctbempenho as empenho', function ($join) {
                $join->on('empenho.id', '=', 'movimento.idempenho')
                    ->on('empenho.idcliente', '=', 'movimento.idcliente');
            })
            ->selectRaw('SUM(movimento.emissao - movimento.anular)')
            ->whereColumn('empenho.iddespesa', 'despesa.id')
            ->whereColumn('movimento.idcliente', 'despesa.idcliente')
            ->where('empenho.tipo', 'O')
            ->groupBy('empenho.iddespesa');

        $subRestos = DB::table('ctbempenhomovimento as movimento')
            ->join('ctbempenho as empenho', function ($join) {
                $join->on('empenho.id', '=', 'movimento.idempenho')
                    ->on('empenho.idcliente', '=', 'movimento.idcliente');
            })
            ->selectRaw('SUM(movimento.emissao - movimento.anular)')
            ->whereColumn('empenho.iddespesa', 'despesa.id')
            ->whereColumn('movimento.idcliente', 'despesa.idcliente')
            ->where('empenho.tipo', 'R')
            ->groupBy('empenho.iddespesa');

        // 2. Query Interna (O conteúdo do parênteses "query")
        $queryInterna = DB::table('ctbcontadespesa as despesa')
            ->join('ctbelemento as elemento', function ($join) {
                $join->on('elemento.id', '=', 'despesa.idelemento')
                    ->on('elemento.idcliente', '=', 'despesa.idcliente');
            })
            ->select(
                'despesa.idcliente as cliente_id',
                'despesa.idelemento as elemento_id',
                'despesa.exercicio as exercicio',
                'elemento.estrutural as estrutural',
                'elemento.nome as descricao'
            )
            ->selectRaw("SUM(({$subOrcado->toSql()})) as valor_orcado")
            ->selectRaw("SUM(({$subCorrigido->toSql()})) as valor_corrigido")
            ->selectRaw("SUM(({$subExecutado->toSql()})) as valor_executado")
            ->selectRaw("SUM(({$subRestos->toSql()})) as valor_restos")
            ->mergeBindings($subOrcado)
            ->mergeBindings($subCorrigido)
            ->mergeBindings($subExecutado)
            ->mergeBindings($subRestos)
            ->where('despesa.idcliente', $idcliente)
            ->where('despesa.exercicio', $exercicio)
            ->groupBy('despesa.idcliente', 'despesa.exercicio', 'despesa.idelemento', 'elemento.estrutural', 'elemento.nome');

        // 3. Query Externa (Cálculos finais sobre a "query" virtual)
        return DB::table(DB::raw("({$queryInterna->toSql()}) as query"))
            ->mergeBindings($queryInterna)
            ->select('query.*')
            ->selectRaw("
            (IFNULL(query.valor_orcado, 0) + IFNULL(query.valor_corrigido, 0) - IFNULL(query.valor_executado, 0)) 
            as valor_restante
        ")
            ->selectRaw("
            IFNULL(
                (IFNULL(query.valor_executado, 0) / NULLIF(IFNULL(query.valor_orcado, 0) + IFNULL(query.valor_corrigido, 0), 0)) * 100, 
                0
            ) as percentual_comprometido
        ")
            ->get();
    }

    public function getExecucaoPorOrgao($exercicio, $idcliente)
    {
        // 1. Definição das Subqueries de Agregação (Base)
        $subOrcado = DB::table('ctbcontadespesaloa as orcamento')
            ->selectRaw('SUM(orcamento.total)')
            ->whereColumn('orcamento.iddespesa', 'despesa.id')
            ->whereColumn('orcamento.idcliente', 'despesa.idcliente')
            ->groupBy('orcamento.iddespesa');

        $subCorrigido = DB::table('ctbcontadespesaextra as decreto')
            ->selectRaw("SUM(IF(decreto.operacao = 'S', decreto.total, -decreto.total))")
            ->whereColumn('decreto.iddespesa', 'despesa.id')
            ->whereColumn('decreto.idcliente', 'despesa.idcliente')
            ->groupBy('decreto.iddespesa');

        $subExecutado = DB::table('ctbempenhomovimento as movimento')
            ->join('ctbempenho as empenho', function ($join) {
                $join->on('empenho.id', '=', 'movimento.idempenho')
                    ->on('empenho.idcliente', '=', 'movimento.idcliente');
            })
            ->selectRaw('SUM(movimento.emissao - movimento.anular)')
            ->whereColumn('empenho.iddespesa', 'despesa.id')
            ->whereColumn('movimento.idcliente', 'despesa.idcliente')
            ->where('empenho.tipo', 'O')
            ->groupBy('empenho.iddespesa');

        $subRestos = DB::table('ctbempenhomovimento as movimento')
            ->join('ctbempenho as empenho', function ($join) {
                $join->on('empenho.id', '=', 'movimento.idempenho')
                    ->on('empenho.idcliente', '=', 'movimento.idcliente');
            })
            ->selectRaw('SUM(movimento.emissao - movimento.anular)')
            ->whereColumn('empenho.iddespesa', 'despesa.id')
            ->whereColumn('movimento.idcliente', 'despesa.idcliente')
            ->where('empenho.tipo', 'R')
            ->groupBy('empenho.iddespesa');

        // 2. Query Interna (A que gera o alias "query")
        $innerQuery = DB::table('ctbcontadespesa as despesa')
            ->join('ctbunidadeorcamentaria as unidade', function ($join) {
                $join->on('unidade.id', '=', 'despesa.idunidadeorcamentaria')
                    ->on('unidade.idcliente', '=', 'despesa.idcliente');
            })
            ->join('ctborgao as orgao', function ($join) {
                $join->on('orgao.id', '=', 'unidade.idorgao')
                    ->on('orgao.idcliente', '=', 'unidade.idcliente');
            })
            ->select(
                'despesa.idcliente as cliente_id',
                'unidade.idorgao as orgao_id',
                'despesa.exercicio as exercicio',
                'orgao.codigo as codigo',
                'orgao.nome as descricao'
            )
            ->selectRaw("SUM(({$subOrcado->toSql()})) as valor_orcado")
            ->selectRaw("SUM(({$subCorrigido->toSql()})) as valor_corrigido")
            ->selectRaw("SUM(({$subExecutado->toSql()})) as valor_executado")
            ->selectRaw("SUM(({$subRestos->toSql()})) as valor_restos")
            // Merge de bindings das subqueries
            ->mergeBindings($subOrcado)
            ->mergeBindings($subCorrigido)
            ->mergeBindings($subExecutado)
            ->mergeBindings($subRestos)
            ->where('despesa.idcliente', $idcliente)
            ->where('despesa.exercicio', $exercicio)
            ->groupBy('despesa.idcliente', 'despesa.exercicio', 'unidade.idorgao', 'orgao.codigo', 'orgao.nome');

        // 3. Query Externa com Cálculos e Ordenação
        return DB::table(DB::raw("({$innerQuery->toSql()}) as query"))
            ->mergeBindings($innerQuery)
            ->select('query.*')
            ->selectRaw("
            (IFNULL(query.valor_orcado, 0) + IFNULL(query.valor_corrigido, 0) - IFNULL(query.valor_executado, 0)) 
            as valor_restante
        ")
            ->selectRaw("
            IFNULL(
                (IFNULL(query.valor_executado, 0) / NULLIF(IFNULL(query.valor_orcado, 0) + IFNULL(query.valor_corrigido, 0), 0)) * 100, 
                0
            ) as percentual_comprometido
        ")
            ->orderBy('percentual_comprometido', 'DESC')
            ->get();
    }

    public function getExecucaoPorRecurso($exercicio, $idcliente)
    {
        // 1. Subqueries de Agregação
        $subOrcado = DB::table('ctbcontadespesaloa as orcamento')
            ->selectRaw('SUM(orcamento.total)')
            ->whereColumn('orcamento.idrecurso', 'recurso.id')
            ->whereColumn('orcamento.idcliente', 'recurso.idcliente')
            ->groupBy('orcamento.idrecurso', 'orcamento.idcliente');

        $subCorrigido = DB::table('ctbcontadespesaextra as decreto')
            ->selectRaw("SUM(IF(decreto.operacao = 'S', decreto.total, -decreto.total))")
            ->whereColumn('decreto.idrecurso', 'recurso.id')
            ->whereColumn('decreto.idcliente', 'recurso.idcliente')
            ->groupBy('decreto.idrecurso', 'decreto.idcliente');

        $subExecutado = DB::table('ctbempenhomovimento as movimento')
            ->join('ctbempenho as empenho', function ($join) {
                $join->on('empenho.id', '=', 'movimento.idempenho')
                    ->on('empenho.idcliente', '=', 'movimento.idcliente');
            })
            ->selectRaw('SUM(movimento.emissao - movimento.anular)')
            ->whereColumn('empenho.idrecurso', 'recurso.id')
            ->whereColumn('movimento.idcliente', 'recurso.idcliente')
            ->where('empenho.tipo', 'O')
            ->groupBy('empenho.idrecurso');

        // 2. Query Interna (Removemos o HAVING daqui para evitar o erro de grouping)
        $innerQuery = DB::table('ctbrecursovinculado as recurso')
            ->select(
                'recurso.id',
                'recurso.idcliente as cliente_id',
                'recurso.exercicio as exercicio',
                'recurso.codigo as codigo',
                'recurso.nome as descricao'
            )
            ->selectSub($subOrcado, 'valor_orcado')
            ->selectSub($subCorrigido, 'valor_corrigido')
            ->selectSub($subExecutado, 'valor_executado')
            ->where('recurso.idcliente', $idcliente)
            ->where('recurso.exercicio', $exercicio);

        // 3. Query Externa
        $queryFinal = DB::table(DB::raw("({$innerQuery->toSql()}) as query"))
            // Limpa os bindings da query externa e importa os da interna na ordem exata
            ->setBindings($innerQuery->getBindings())
            ->select('query.*')
            ->selectRaw("
        (IFNULL(query.valor_orcado, 0) + IFNULL(query.valor_corrigido, 0) - IFNULL(query.valor_executado, 0)) 
        as valor_restante
    ")
            ->selectRaw("
        IFNULL(
            (IFNULL(query.valor_executado, 0) / NULLIF(IFNULL(query.valor_orcado, 0) + IFNULL(query.valor_corrigido, 0), 0)) * 100, 
            0
        ) as percentual_comprometido
    ")
            ->whereNotNull('query.valor_orcado')
            ->orderBy('percentual_comprometido', 'DESC')
            ->orderBy('codigo', 'ASC');

        return $queryFinal->get();
    }

    public function getExecucaoPorLocalizador($exercicio, $idcliente)
    {
        // 1. Subqueries de Agregação (Baseadas em iddespesa)
        $subOrcado = DB::table('ctbcontadespesaloa as orcamento')
            ->selectRaw('SUM(orcamento.total)')
            ->whereColumn('orcamento.iddespesa', 'despesa.id')
            ->whereColumn('orcamento.idcliente', 'despesa.idcliente')
            ->groupBy('orcamento.iddespesa');

        $subCorrigido = DB::table('ctbcontadespesaextra as decreto')
            ->selectRaw("SUM(IF(decreto.operacao = 'S', decreto.total, -decreto.total))")
            ->whereColumn('decreto.iddespesa', 'despesa.id')
            ->whereColumn('decreto.idcliente', 'despesa.idcliente')
            ->groupBy('decreto.iddespesa');

        $subExecutado = DB::table('ctbempenhomovimento as movimento')
            ->join('ctbempenho as empenho', function ($join) {
                $join->on('empenho.id', '=', 'movimento.idempenho')
                    ->on('empenho.idcliente', '=', 'movimento.idcliente');
            })
            ->selectRaw('SUM(movimento.emissao - movimento.anular)')
            ->whereColumn('empenho.iddespesa', 'despesa.id')
            ->whereColumn('movimento.idcliente', 'despesa.idcliente')
            ->where('empenho.tipo', 'O')
            ->groupBy('empenho.iddespesa');

        $subRestos = DB::table('ctbempenhomovimento as movimento')
            ->join('ctbempenho as empenho', function ($join) {
                $join->on('empenho.id', '=', 'movimento.idempenho')
                    ->on('empenho.idcliente', '=', 'movimento.idcliente');
            })
            ->selectRaw('SUM(movimento.emissao - movimento.anular)')
            ->whereColumn('empenho.iddespesa', 'despesa.id')
            ->whereColumn('movimento.idcliente', 'despesa.idcliente')
            ->where('empenho.tipo', 'R')
            ->groupBy('empenho.iddespesa');

        // 2. Query Interna (query)
        $innerQuery = DB::table('ctbcontadespesa as despesa')
            ->join('ctbprojeto as localizador', function ($join) {
                $join->on('localizador.id', '=', 'despesa.idprojeto')
                    ->on('localizador.idcliente', '=', 'despesa.idcliente');
            })
            ->select(
                'despesa.idcliente as cliente_id',
                'despesa.idprojeto as localizador_id',
                'despesa.exercicio as exercicio',
                'localizador.codigo as codigo',
                'localizador.nome as descricao'
            )
            ->selectRaw("SUM(({$subOrcado->toSql()})) as valor_orcado")
            ->selectRaw("SUM(({$subCorrigido->toSql()})) as valor_corrigido")
            ->selectRaw("SUM(({$subExecutado->toSql()})) as valor_executado")
            ->selectRaw("SUM(({$subRestos->toSql()})) as valor_restos")
            ->mergeBindings($subOrcado)
            ->mergeBindings($subCorrigido)
            ->mergeBindings($subExecutado)
            ->mergeBindings($subRestos)
            ->where('despesa.idcliente', $idcliente)
            ->where('despesa.exercicio', $exercicio)
            ->groupBy('despesa.idcliente', 'despesa.exercicio', 'despesa.idprojeto', 'localizador.codigo', 'localizador.nome');

        // 3. Query Externa com Cálculos e Correção de Bindings
        return DB::table(DB::raw("({$innerQuery->toSql()}) as query"))
            ->setBindings($innerQuery->getBindings()) // FORÇA a sincronia exata dos parâmetros
            ->select('query.*')
            ->selectRaw("
            (IFNULL(query.valor_orcado, 0) + IFNULL(query.valor_corrigido, 0) - IFNULL(query.valor_executado, 0)) 
            as valor_restante
        ")
            ->selectRaw("
            IFNULL(
                (IFNULL(query.valor_executado, 0) / NULLIF(IFNULL(query.valor_orcado, 0) + IFNULL(query.valor_corrigido, 0), 0)) * 100, 
                0
            ) as percentual_comprometido
        ")
            ->orderBy('percentual_comprometido', 'DESC')
            ->get();
    }
}
