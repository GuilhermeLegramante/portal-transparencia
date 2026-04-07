<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class ReceitaRepository
{
    public function getResumoGeralPorExercicio($idcliente)
    {
        // 1. Soma o Orçamento por Cliente e Exercício
        $subOrcado = DB::table('ctbcontareceitaloa as orcamento')
            ->join('ctbcontareceita as r', 'r.id', '=', 'orcamento.idreceita')
            ->select('r.idcliente', 'r.exercicio', DB::raw('SUM(orcamento.total) as total_orcado'))
            ->where('r.idcliente', $idcliente)
            ->groupBy('r.idcliente', 'r.exercicio');

        // 2. Soma a Arrecadação por Cliente e Exercício
        $subExecutado = DB::table('ctbcontareceitamovimento as movimento')
            ->join('ctbcontareceita as r', 'r.id', '=', 'movimento.idreceita')
            ->select('r.idcliente', 'r.exercicio', DB::raw('SUM(movimento.total) as total_executado'))
            ->where('r.idcliente', $idcliente)
            ->groupBy('r.idcliente', 'r.exercicio');

        // 3. Query Principal unindo os resumos
        return DB::table('ctbcontareceita as receita')
            ->leftJoinSub($subOrcado, 'orc', function ($join) {
                $join->on('receita.idcliente', '=', 'orc.idcliente')
                    ->on('receita.exercicio', '=', 'orc.exercicio');
            })
            ->leftJoinSub($subExecutado, 'exec', function ($join) {
                $join->on('receita.idcliente', '=', 'exec.idcliente')
                    ->on('receita.exercicio', '=', 'exec.exercicio');
            })
            ->select(
                'receita.idcliente as cliente_id',
                'receita.exercicio as exercicio',
                DB::raw('IFNULL(orc.total_orcado, 0) as valor_orcado'),
                DB::raw('IFNULL(exec.total_executado, 0) as valor_executado'),
                DB::raw('IFNULL((exec.total_executado / NULLIF(orc.total_orcado, 0)) * 100, 0) as percentual_realizado')
            )
            ->where('receita.idcliente', $idcliente)
            ->groupBy('receita.idcliente', 'receita.exercicio', 'orc.total_orcado', 'exec.total_executado')
            ->orderBy('receita.exercicio', 'desc')
            ->get();
    }

    public function getArrecadadaPorElemento($idcliente, $exercicio)
    {
        return DB::table('ctbcontareceitamovimento as movimento')
            ->join('ctbcontareceita as contaReceita', function ($join) {
                $join->on('contaReceita.id', '=', 'movimento.idreceita')
                    ->on('contaReceita.idcliente', '=', 'movimento.idcliente');
            })
            ->join('ctbelemento as elemento', function ($join) {
                $join->on('elemento.id', '=', 'contaReceita.idelemento')
                    ->on('elemento.idcliente', '=', 'contaReceita.idcliente');
            })
            ->select(
                'contaReceita.idcliente as cliente_id',
                'contaReceita.idelemento as elemento_id',
                'contaReceita.exercicio as exercicio',
                'elemento.estrutural as estrutural',
                'elemento.nome as descricao'
            )
            // Valor Arrecadado (Natureza 'A')
            ->selectRaw("SUM(IF(contaReceita.natureza = 'A', movimento.total, 0.00)) as valor_arrecadado")

            // Valor Deduzido (Natureza 'D' - usando ABS para garantir valor positivo na coluna de dedução)
            ->selectRaw("SUM(IF(contaReceita.natureza = 'D', ABS(movimento.total), 0.00)) as valor_deduzido")

            // Saldo Final Arrecadado (Soma real que abate as deduções automaticamente pelo sinal do movimento)
            ->selectRaw("SUM(movimento.total) as saldo_arrecadado")

            ->where('movimento.idcliente', $idcliente)
            ->where('contaReceita.exercicio', $exercicio)
            ->groupBy(
                'movimento.idcliente',
                'contaReceita.idcliente',
                'contaReceita.exercicio',
                'contaReceita.idelemento',
                'elemento.estrutural',
                'elemento.nome'
            )
            ->orderBy('valor_arrecadado', 'DESC')
            ->get();
    }

    public function getArrecadadaPorElementoDetalhes($idcliente, $exercicio, $idelemento)
    {
        return DB::table('ctbcontareceitamovimento as movimento')
            ->join('ctbcontareceita as contaReceita', function ($join) {
                $join->on('contaReceita.id', '=', 'movimento.idreceita')
                    ->on('contaReceita.idcliente', '=', 'movimento.idcliente');
            })
            ->select(
                'contaReceita.idcliente as cliente_id',
                'contaReceita.idelemento as elemento_id',
                'movimento.mes as mes',
                'contaReceita.exercicio as exercicio'
            )
            // Valor Arrecadado (Natureza 'A')
            ->selectRaw("SUM(IF(contaReceita.natureza = 'A', movimento.total, 0.00)) as valor_arrecadado")

            // Valor Deduzido (Natureza 'D') - Aplicando o ABS no SUM conforme seu SQL
            ->selectRaw("ABS(SUM(IF(contaReceita.natureza = 'D', movimento.total, 0.00))) as valor_deduzido")

            // Saldo Líquido do mês
            ->selectRaw("SUM(movimento.total) as saldo")

            ->where('movimento.idcliente', $idcliente)
            ->where('contaReceita.exercicio', $exercicio)
            ->where('contaReceita.idelemento', $idelemento)

            ->groupBy('cliente_id', 'mes', 'exercicio', 'contaReceita.idelemento')
            ->orderBy('mes', 'ASC')
            ->get();
    }

    public function getArrecadadaPorRecurso($idcliente, $exercicio)
    {
        return DB::table('ctbcontareceitamovimento as movimento')
            ->join('ctbcontareceita as contaReceita', function ($join) {
                $join->on('contaReceita.id', '=', 'movimento.idreceita')
                    ->on('contaReceita.idcliente', '=', 'movimento.idcliente');
            })
            ->join('ctbrecursovinculado as recurso', function ($join) {
                $join->on('recurso.id', '=', 'movimento.idrecurso')
                    ->on('recurso.idcliente', '=', 'movimento.idcliente');
            })
            ->select(
                'movimento.idcliente as cliente_id',
                'movimento.idrecurso as recurso_id',
                'recurso.exercicio as exercicio',
                'recurso.codigo as codigo',
                'recurso.nome as descricao'
            )
            // Valor Arrecadado Bruto (Natureza 'A')
            ->selectRaw("SUM(IF(contaReceita.natureza = 'A', movimento.total, 0.00)) as valor_arrecadado")

            // Valor Deduzido (Natureza 'D')
            ->selectRaw("SUM(IF(contaReceita.natureza = 'D', ABS(movimento.total), 0.00)) as valor_deduzido")

            // Saldo Arrecadado Líquido
            ->selectRaw("SUM(movimento.total) as saldo_arrecadado")

            ->where('movimento.idcliente', $idcliente)
            ->where('recurso.exercicio', $exercicio)
            ->groupBy(
                'movimento.idcliente',
                'movimento.idrecurso',
                'recurso.exercicio',
                'recurso.codigo',
                'recurso.nome'
            )
            ->orderBy('valor_arrecadado', 'DESC')
            ->get();
    }

    public function getArrecadadaPorRecursoDetalhes($idcliente, $exercicio, $idrecurso)
    {
        return DB::table('ctbcontareceitamovimento as movimento')
            ->join('ctbcontareceita as contaReceita', function ($join) {
                $join->on('contaReceita.id', '=', 'movimento.idreceita')
                    ->on('contaReceita.idcliente', '=', 'movimento.idcliente');
            })
            ->select(
                'contaReceita.idcliente as cliente_id',
                'movimento.idrecurso as recurso_id',
                'movimento.mes as mes',
                'contaReceita.exercicio as exercicio'
            )
            // Valor Arrecadado Bruto (Natureza 'A')
            ->selectRaw("SUM(IF(contaReceita.natureza = 'A', movimento.total, 0.00)) as valor_arrecadado")

            // Valor Deduzido (Natureza 'D') - Usando ABS para exibição positiva
            ->selectRaw("SUM(IF(contaReceita.natureza = 'D', ABS(movimento.total), 0.00)) as valor_deduzido")

            // Saldo Líquido Mensal
            ->selectRaw("SUM(movimento.total) as saldo")

            ->where('movimento.idcliente', $idcliente)
            ->where('contaReceita.exercicio', $exercicio)
            ->where('movimento.idrecurso', $idrecurso)

            ->groupBy('cliente_id', 'mes', 'exercicio', 'movimento.idrecurso')
            ->orderBy('mes', 'ASC')
            ->get();
    }

    public function getExecutadaPorElemento($idcliente, $exercicio)
    {
        // 1. Subquery para Valor Orçado (LOA)
        $subOrcado = DB::table('ctbcontareceitaloa as orcamento')
            ->selectRaw('IFNULL(SUM(orcamento.total), 0.00)')
            ->whereColumn('orcamento.idreceita', 'contaReceita.id')
            ->whereColumn('orcamento.idcliente', 'contaReceita.idcliente')
            ->groupBy('orcamento.idreceita', 'orcamento.idcliente');

        // 2. Subquery para Valor Executado (Movimentação)
        $subExecutado = DB::table('ctbcontareceitamovimento as movimento')
            ->selectRaw('IFNULL(SUM(movimento.total), 0.00)')
            ->whereColumn('movimento.idreceita', 'contaReceita.id')
            ->whereColumn('movimento.idcliente', 'contaReceita.idcliente')
            ->groupBy('movimento.idreceita', 'movimento.idcliente');

        // 3. Query Principal
        return DB::table('ctbcontareceita as contaReceita')
            ->join('ctbelemento as elemento', function ($join) {
                $join->on('elemento.id', '=', 'contaReceita.idelemento')
                    ->on('elemento.idcliente', '=', 'contaReceita.idcliente');
            })
            ->select(
                'contaReceita.idcliente as cliente_id',
                'contaReceita.idelemento as elemento_id',
                'contaReceita.exercicio as exercicio',
                'elemento.estrutural as estrutural',
                'elemento.nome as descricao'
            )
            // Injeção das subqueries no SELECT
            ->selectSub($subOrcado, 'valor_orcado')
            ->selectSub($subExecutado, 'valor_executado')

            ->where('contaReceita.idcliente', $idcliente)
            ->where('contaReceita.exercicio', $exercicio)
            ->get();
    }

    public function getExecutadaPorRecurso($idcliente, $exercicio)
    {
        // 1. Subquery para Valor Orçado (LOA)
        $subOrcado = DB::table('ctbcontareceitaloa as orcamento')
            ->selectRaw('SUM(orcamento.total)')
            ->whereColumn('orcamento.idcliente', 'recurso.idcliente')
            ->whereColumn('orcamento.idrecurso', 'recurso.id')
            ->groupBy('orcamento.idcliente', 'orcamento.idrecurso');

        // 2. Subquery para Valor Executado (Movimentação)
        $subExecutado = DB::table('ctbcontareceitamovimento as movimento')
            ->selectRaw('SUM(movimento.total)')
            ->whereColumn('movimento.idcliente', 'recurso.idcliente')
            ->whereColumn('movimento.idrecurso', 'recurso.id')
            ->groupBy('movimento.idcliente', 'movimento.idrecurso');

        // 3. Query Interna (Base)
        $innerQuery = DB::table('ctbrecursovinculado as recurso')
            ->select(
                'recurso.id as recurso_id',
                'recurso.idcliente as cliente_id',
                'recurso.exercicio as exercicio',
                'recurso.codigo as codigo',
                'recurso.nome as descricao'
            )
            ->selectSub($subOrcado, 'valor_orcado')
            ->selectSub($subExecutado, 'valor_executado')
            ->where('recurso.idcliente', $idcliente)
            ->where('recurso.exercicio', $exercicio);

        // 4. Query Externa com Filtro HAVING (via Where)
        return DB::table(DB::raw("({$innerQuery->toSql()}) as query"))
            ->setBindings($innerQuery->getBindings())
            ->select('query.*')
            // Simula o HAVING: valor_orcado IS NOT NULL OR valor_executado IS NOT NULL
            ->where(function ($query) {
                $query->whereNotNull('query.valor_orcado')
                    ->orWhereNotNull('query.valor_executado');
            })
            ->get();
    }
}
