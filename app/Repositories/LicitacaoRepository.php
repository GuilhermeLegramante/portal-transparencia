<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class LicitacaoRepository
{
    /**
     * Resumo quantitativo por exercício
     */
    public function getResumoLicitacoesPorExercicio($idcliente)
    {
        return DB::table('liclicitacao as licitacao')
            ->select(
                'licitacao.idcliente as cliente_id',
                'licitacao.exercicio as exercicio'
            )
            ->selectRaw("SUM(IF(licitacao.situacao = 7, 1, 0)) AS edital")
            ->selectRaw("SUM(IF(licitacao.situacao = 0, 1, 0)) AS aberta")
            ->selectRaw("SUM(IF(licitacao.situacao = 6, 1, 0)) AS suspensa")
            ->selectRaw("SUM(IF(licitacao.situacao NOT IN (0, 6, 7), 1, 0)) AS finalizada")
            ->selectRaw("COUNT(*) AS total")
            ->where('licitacao.idcliente', $idcliente)
            ->groupBy('licitacao.idcliente', 'licitacao.exercicio')
            ->orderBy('licitacao.exercicio', 'DESC')
            ->get();
    }

    /**
     * Listagem detalhada das licitações de um exercício específico
     */
    public function getLicitacoesPorExercicio($idcliente, $exercicio)
    {
        // Subquery para calcular o valor total (Soma dos itens x lances)
        $subValorTotal = DB::table('liclicitacaoproponente as proponente')
            ->join('liclicitacaoitem as item', function ($join) {
                $join->on('item.id', '=', 'proponente.iditem')
                    ->on('item.idcliente', '=', 'proponente.idcliente');
            })
            ->select('item.idlicitacao', 'item.idcliente')
            ->selectRaw('SUM(item.quantidade * proponente.valorunitario) as total')
            ->groupBy('item.idlicitacao', 'item.idcliente');

        return DB::table('liclicitacao as licitacao')
            ->leftJoin('ctbmodalidadelicitacao as modalidade', function ($join) {
                $join->on('modalidade.id', '=', 'licitacao.idmodalidade')
                    ->on('modalidade.idcliente', '=', 'licitacao.idcliente');
            })
            ->leftJoinSub($subValorTotal, 'vlr', function ($join) {
                $join->on('vlr.idlicitacao', '=', 'licitacao.id')
                    ->on('vlr.idcliente', '=', 'licitacao.idcliente');
            })
            ->select(
                'licitacao.id as licitacao_id',
                'licitacao.idcliente as cliente_id',
                'licitacao.exercicio',
                'licitacao.dataabertura as data_abertura',
                'licitacao.edital as numero_edital',
                'licitacao.processo as numero_processo',
                'licitacao.descricao as objeto',
                'licitacao.modalidade as tipo_id',
                'licitacao.situacao as situacao_id',
                'modalidade.nome as modalidade_nome',
                DB::raw('IFNULL(vlr.total, 0.00) as valor_total')
            )
            ->where('licitacao.idcliente', $idcliente)
            ->where('licitacao.exercicio', $exercicio)
            ->orderBy('licitacao.dataabertura', 'DESC')
            ->get();
    }

    /**
     * Detalhes de uma licitação específica
     */
    public function getLicitacaoDetalhes($idcliente, $id)
    {
        return DB::table('liclicitacao as licitacao')
            ->leftJoin('ctbmodalidadelicitacao as modalidade', 'modalidade.id', '=', 'licitacao.idmodalidade')
            ->select('licitacao.*', 'modalidade.nome as modalidade_nome')
            ->where('licitacao.idcliente', $idcliente)
            ->where('licitacao.id', $id)
            ->first();
    }

    // --- Comissão Julgadora ---
    public function getComissaoLicitacao($idcliente, $idlicitacao)
    {
        return DB::table('liclicitacaocomissaointegrante as integrante')
            ->leftJoin('cadmunicipe as municipe', function ($join) {
                $join->on('integrante.idmembro', '=', 'municipe.id')
                    ->on('integrante.idcliente', '=', 'municipe.idcliente');
            })
            ->select(
                'integrante.idmembro as inscricao',
                'municipe.nome',
                'integrante.cargo',
                DB::raw("(SELECT h.nome FROM cadcomissaointegrante m 
                      INNER JOIN cadcomissaohierarquia h ON h.id = m.idhierarquia AND h.idcliente = m.idcliente 
                      WHERE m.idcliente = integrante.idcliente AND m.idcomissao = integrante.idcomissao 
                      AND m.idintegrante = integrante.idmembro) as hierarquia")
            )
            ->where('integrante.idcliente', $idcliente)
            ->where('integrante.idlicitacao', $idlicitacao)
            ->orderBy('hierarquia', 'DESC')
            ->get();
    }

    // --- Itens Licitados ---
    public function getItensLicitacao($idcliente, $idlicitacao)
    {
        return DB::table('liclicitacaoitem as item')
            ->select(
                'item.id',
                'item.numero',
                'item.complemento',
                'item.quantidade',
                'item.situacao',
                'item.tipo',
                DB::raw("IF(item.idproduto IS NOT NULL, 
                (SELECT p.nome FROM almproduto p WHERE item.idproduto = p.id AND item.idcliente = p.idcliente),
                (SELECT s.nome FROM comservico s WHERE item.idservico = s.id AND item.idcliente = s.idcliente)) as descricao"),
                DB::raw("(SELECT pr.valorunitario FROM liclicitacaoproponente pr 
                      WHERE pr.idcliente = item.idcliente AND pr.iditem = item.id LIMIT 1) as valor_unitario")
            )
            ->where('item.idcliente', $idcliente)
            ->where('item.idlicitacao', $idlicitacao)
            ->orderBy('item.numero')
            ->get();
    }

    // --- Vencedores (Agrupados por Proponente) ---
    public function getVencedoresLicitacao($idcliente, $idlicitacao)
    {
        return DB::table('liclicitacaoproponente as proponente')
            ->join('liclicitacaoitem as item', function ($join) {
                $join->on('item.id', '=', 'proponente.iditem')
                    ->on('item.idcliente', '=', 'proponente.idcliente');
            })
            ->leftJoin('cadmunicipe as municipe', function ($join) {
                $join->on('proponente.idproponente', '=', 'municipe.id')
                    ->on('proponente.idcliente', '=', 'municipe.idcliente');
            })
            ->select(
                'municipe.nome as proponente_nome',
                'item.numero',
                'item.quantidade',
                'proponente.valorunitario as valor_unitario',
                DB::raw("(proponente.valorunitario * item.quantidade) as valor_total"),
                DB::raw("IF(item.idproduto IS NOT NULL, 
                (SELECT p.nome FROM almproduto p WHERE item.idproduto = p.id AND item.idcliente = p.idcliente),
                (SELECT s.nome FROM comservico s WHERE item.idservico = s.id AND item.idcliente = s.idcliente)) as descricao")
            )
            ->where('proponente.idcliente', $idcliente)
            ->where('item.idlicitacao', $idlicitacao)
            ->get()
            ->groupBy('proponente_nome'); // Agrupamento Master/Detail no PHP
    }
}
