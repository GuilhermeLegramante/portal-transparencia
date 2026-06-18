<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class FolhaPagamentoRepository
{
    // Resumo Geral (Exercícios)
    public function getResumoFinanceiro($idcliente)
    {
        return DB::table('folcalculo as calculo')
            ->join('folevento as evento', function ($j) {
                $j->on('evento.id', '=', 'calculo.idevento')->on('evento.idcliente', '=', 'calculo.idcliente');
            })
            ->select('calculo.exercicio')
            ->selectRaw("SUM(IF(evento.tipo = 'P', calculo.valor, 0.00)) AS valor_bruto")
            ->selectRaw("SUM(IF(evento.tipo = 'P', calculo.valor, -calculo.valor)) AS valor_liquido")
            ->where('calculo.idcliente', $idcliente)
            ->whereIn('evento.tipo', ['P', 'D'])
            ->groupBy('calculo.idcliente', 'calculo.exercicio')
            ->orderByDesc('calculo.exercicio')
            ->get();
    }

    // Listagem por Função (Agrupada)
    public function getPorFuncao($idcliente, $exercicio, $mes)
    {
        return DB::table('folcalculo as calculo')
            ->join('folcontrato as contrato', 'contrato.id', '=', 'calculo.idcontrato')
            ->join('folfuncao as funcao', 'funcao.id', '=', 'contrato.idfuncao')
            ->join('folevento as evento', 'evento.id', '=', 'calculo.idevento')
            ->select('funcao.id as funcao_id', 'funcao.codigo', 'funcao.nome as descricao')
            ->selectRaw('COUNT(DISTINCT calculo.idcontrato) AS quantidade')
            ->selectRaw("SUM(IF(evento.tipo = 'P', calculo.valor, 0.00)) AS total_provento")
            ->selectRaw("SUM(IF(evento.tipo = 'D', calculo.valor, 0.00)) AS total_desconto")
            ->where('calculo.idcliente', $idcliente)
            ->where('calculo.exercicio', $exercicio)
            ->where('calculo.mes', $mes)
            ->groupBy('funcao.id', 'funcao.codigo', 'funcao.nome')
            ->get();
    }

    // Detalhe de Contratos por Categoria (Função, Lotação ou Regime)
    public function getContratosPorCategoria($idcliente, $exercicio, $mes, $campoFiltro, $idFiltro)
    {
        return DB::table('folcalculo as calculo')
            ->join('folcontrato as contrato', function ($j) {
                $j->on('contrato.id', '=', 'calculo.idcontrato')
                    ->on('contrato.idcliente', '=', 'calculo.idcliente');
            })
            ->join('cadmunicipe as municipe', function ($j) {
                $j->on('municipe.id', '=', 'contrato.idservidor')
                    ->on('municipe.idcliente', '=', 'contrato.idcliente');
            })
            ->join('folevento as evento', function ($j) {
                $j->on('evento.id', '=', 'calculo.idevento')
                    ->on('evento.idcliente', '=', 'calculo.idcliente');
            })
            ->select(
                'contrato.id',
                'contrato.matricula',
                'contrato.admissao',
                'municipe.nome',
                'municipe.id as inscricao'
            )
            ->selectRaw("SUM(IF(evento.tipo = 'P', calculo.valor, 0.00)) AS total_provento")
            ->selectRaw("SUM(IF(evento.tipo = 'D', calculo.valor, 0.00)) AS total_desconto")
            ->where('calculo.idcliente', $idcliente)
            ->where('calculo.exercicio', $exercicio)
            ->where('calculo.mes', $mes)
            ->where("contrato.{$campoFiltro}", $idFiltro) // Aqui entra 'idlotacao' dinamicamente
            ->groupBy('contrato.id', 'contrato.matricula', 'contrato.admissao', 'municipe.nome', 'municipe.id')
            ->get();
    }

    // Listagem por Regime (Agrupada - Nível 1)
    public function getPorRegime($idcliente, $exercicio, $mes)
    {
        return DB::table('folcalculo as calculo')
            ->join('folcontrato as contrato', function ($j) {
                $j->on('contrato.id', '=', 'calculo.idcontrato')
                    ->on('contrato.idcliente', '=', 'calculo.idcliente');
            })
            ->join('folregime as regime', function ($j) {
                $j->on('regime.id', '=', 'contrato.idregime')
                    ->on('regime.idcliente', '=', 'contrato.idcliente');
            })
            ->join('folevento as evento', function ($j) {
                $j->on('evento.id', '=', 'calculo.idevento')
                    ->on('evento.idcliente', '=', 'calculo.idcliente');
            })
            ->select(
                'regime.id as regime_id',
                'regime.codigo',
                'regime.nome as descricao'
            )
            ->selectRaw('COUNT(DISTINCT calculo.idcontrato) AS quantidade')
            ->selectRaw("SUM(IF(evento.tipo = 'P', calculo.valor, 0.00)) AS total_provento")
            ->selectRaw("SUM(IF(evento.tipo = 'D', calculo.valor, 0.00)) AS total_desconto")
            ->where('calculo.idcliente', $idcliente)
            ->where('calculo.exercicio', $exercicio)
            ->where('calculo.mes', $mes)
            ->groupBy('regime.id', 'regime.codigo', 'regime.nome')
            ->orderBy('regime.codigo')
            ->get();
    }

    // Listagem por Lotação (Agrupada - Nível 1)
    public function getPorLotacao($idcliente, $exercicio, $mes)
    {
        return DB::table('folcalculo as calculo')
            ->join('folcontrato as contrato', function ($j) {
                $j->on('contrato.id', '=', 'calculo.idcontrato')
                    ->on('contrato.idcliente', '=', 'calculo.idcliente');
            })
            ->join('follotacao as lotacao', function ($j) {
                $j->on('lotacao.id', '=', 'contrato.idlotacao')
                    ->on('lotacao.idcliente', '=', 'calculo.idcliente');
            })
            ->join('folevento as evento', function ($j) {
                $j->on('evento.id', '=', 'calculo.idevento')
                    ->on('evento.idcliente', '=', 'calculo.idcliente');
            })
            ->select(
                'lotacao.id as lotacao_id',
                'lotacao.codigo',
                'lotacao.nome as descricao'
            )
            // Subquery para trazer o nome da Unidade Orçamentária
            ->selectRaw("(SELECT u.nome FROM ctbunidadeorcamentaria u WHERE u.id = lotacao.idunidade AND u.idcliente = lotacao.idcliente LIMIT 1) as unidade_nome")
            ->selectRaw('COUNT(DISTINCT calculo.idcontrato) AS quantidade')
            ->selectRaw("SUM(IF(evento.tipo = 'P', calculo.valor, 0.00)) AS total_provento")
            ->selectRaw("SUM(IF(evento.tipo = 'D', calculo.valor, 0.00)) AS total_desconto")
            ->where('calculo.idcliente', $idcliente)
            ->where('calculo.exercicio', $exercicio)
            ->where('calculo.mes', $mes)
            ->groupBy('lotacao.id', 'lotacao.codigo', 'lotacao.nome', 'lotacao.idunidade', 'lotacao.idcliente')
            ->orderBy('lotacao.codigo')
            ->get();
    }

    public function getPorServidor($idcliente, $exercicio, $mes, $busca = null)
    {
        $query = DB::table('folcalculo as calculo')
            ->join('folcontrato as contrato', function ($j) {
                $j->on('contrato.id', '=', 'calculo.idcontrato')
                    ->on('contrato.idcliente', '=', 'calculo.idcliente');
            })
            ->join('cadmunicipe as municipe', function ($j) {
                $j->on('municipe.id', '=', 'contrato.idservidor')
                    ->on('municipe.idcliente', '=', 'contrato.idcliente');
            })
            ->join('folevento as evento', function ($j) {
                $j->on('evento.id', '=', 'calculo.idevento')
                    ->on('evento.idcliente', '=', 'calculo.idcliente');
            })
            ->select(
                'contrato.id as contrato_id',
                'contrato.matricula',
                'municipe.nome',
                'municipe.cpf'
            )
            ->selectRaw("SUM(IF(evento.tipo = 'P', calculo.valor, 0.00)) AS total_provento")
            ->selectRaw("SUM(IF(evento.tipo = 'D', calculo.valor, 0.00)) AS total_desconto")
            ->where('calculo.idcliente', $idcliente)
            ->where('calculo.exercicio', $exercicio)
            ->where('calculo.mes', $mes);

        if ($busca) {
            $query->where(function ($q) use ($busca) {
                $q->where('municipe.nome', 'like', "%{$busca}%")
                    ->orWhere('contrato.matricula', 'like', "%{$busca}%")
                    ->orWhere('municipe.cpf', 'like', "%{$busca}%");
            });
        }

        return $query->groupBy('contrato.id', 'contrato.matricula', 'municipe.nome', 'municipe.cpf')
            ->orderBy('municipe.nome')
            ->paginate(20); // Por servidor costuma ter muitos dados, paginação é ideal
    }

    // Contracheque (Itens do Cálculo)
    public function getItensContracheque($idcliente, $exercicio, $mes, $idContrato)
    {
        return DB::table('folcalculo as calculo')
            ->join('folevento as evento', function ($j) {
                $j->on('evento.id', '=', 'calculo.idevento')
                    ->on('evento.idcliente', '=', 'calculo.idcliente'); // Cláusula adicionada aqui
            })
            ->select('evento.tipo', 'evento.confidencial')
            ->selectRaw("IF(evento.confidencial, '****', evento.codigo) AS codigo")
            ->selectRaw("IF(evento.confidencial, 'INFORMAÇÕES PRIVADAS', evento.nome) AS descricao")
            ->selectRaw("SUM(IF(evento.confidencial, 0.00, calculo.referencia)) AS referencia")
            ->selectRaw("SUM(calculo.valor) AS valor")
            ->where('calculo.idcliente', $idcliente)
            ->where('calculo.exercicio', $exercicio)
            ->where('calculo.mes', $mes)
            ->where('calculo.idcontrato', $idContrato)
            ->whereIn('evento.tipo', ['P', 'D'])
            ->groupBy('evento.tipo', 'evento.confidencial', 'codigo', 'descricao')
            ->orderByRaw("CASE evento.tipo WHEN 'P' THEN 1 WHEN 'D' THEN 2 WHEN 'I' THEN 3 ELSE 4 END")
            ->orderBy('evento.codigo')
            ->get();
    }

    public function getDadosContratoContracheque($idcliente, $idContrato)
    {
        return DB::table('folcontrato as contrato')
            ->join('cadmunicipe as municipe', function ($j) {
                $j->on('municipe.id', '=', 'contrato.idservidor')
                    ->on('municipe.idcliente', '=', 'contrato.idcliente');
            })
            ->select(
                'contrato.id',
                'contrato.matricula',
                'contrato.admissao',
                'municipe.nome',
                'municipe.id as inscricao',
                'municipe.cpf'
            )
            ->selectRaw("(SELECT nome FROM folfuncao WHERE id = contrato.idfuncao AND idcliente = contrato.idcliente) as funcao")
            ->selectRaw("(SELECT nome FROM follotacao WHERE id = contrato.idlotacao AND idcliente = contrato.idcliente) as lotacao")
            ->where('contrato.idcliente', $idcliente)
            ->where('contrato.id', $idContrato)
            ->first();
    }
}
