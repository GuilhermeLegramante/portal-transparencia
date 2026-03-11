<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DespesaController extends Controller
{
    public function resumoAnualDiarias(Request $request)
    {
        $idCliente = env('CLIENT_ID');

        $resumoAnual = DB::table('ctbempenhomovimento as movimento')
            ->join('ctbempenho as empenho', function ($join) {
                $join->on('empenho.id', '=', 'movimento.idempenho')
                    ->on('empenho.idcliente', '=', 'movimento.idcliente');
            })
            ->join('ctbcontadespesa as contaDespesa', function ($join) {
                $join->on('contaDespesa.id', '=', 'empenho.iddespesa')
                    ->on('contaDespesa.idcliente', '=', 'empenho.idcliente');
            })
            ->join('ctbelemento as elemento', function ($join) {
                $join->on('elemento.id', '=', 'contaDespesa.idelemento')
                    ->on('elemento.idcliente', '=', 'contaDespesa.idcliente');
            })
            ->select([
                'movimento.idcliente as cliente_id',
                'empenho.exercicio as exercicio',
            ])
            ->selectRaw("SUM(movimento.emissao - movimento.anular) as valor")
            ->where('movimento.idcliente', $idCliente)
            ->where(function ($query) {
                $query->where('elemento.estrutural', 'like', '3.3.9.0.14.%')
                    ->orWhere('elemento.estrutural', 'like', '3.3.9.2.14.%')
                    ->orWhere('elemento.estrutural', 'like', '3.3.9.5.14.%')
                    ->orWhere('elemento.estrutural', 'like', '3.3.9.6.14.%')
                    ->orWhere('elemento.estrutural', 'like', '4.4.9.0.14.%');
            })
            ->groupBy('movimento.idcliente', 'empenho.exercicio')
            ->orderByDesc('empenho.exercicio')
            ->get();

        $breadcrumbTitle = 'Diárias - Resumo por Exercício';

        return view('despesa.diarias.index', compact('resumoAnual', 'breadcrumbTitle'));
    }

    public function detalheDiarias($exercicio)
    {
        $idCliente = env('CLIENT_ID');

        // 1. Query Base (Igual ao anterior, garantindo todos no GroupBy)
        $subQuery = DB::table('ctbempenhomovimento as movimento')
            ->join('ctbempenho as empenho', function ($join) {
                $join->on('empenho.id', '=', 'movimento.idempenho')
                    ->on('empenho.idcliente', '=', 'movimento.idcliente');
            })
            ->join('ctbcontadespesa as contaDespesa', function ($join) {
                $join->on('contaDespesa.id', '=', 'empenho.iddespesa')
                    ->on('contaDespesa.idcliente', '=', 'empenho.idcliente');
            })
            ->join('ctbelemento as elemento', function ($join) {
                $join->on('elemento.id', '=', 'contaDespesa.idelemento')
                    ->on('elemento.idcliente', '=', 'contaDespesa.idcliente');
            })
            ->select([
                'movimento.idcliente',
                'empenho.idcredor',
                'empenho.exercicio',
            ])
            ->selectRaw("
            (SELECT municipe.nome 
             FROM cadmunicipe municipe 
             WHERE empenho.idcredor = municipe.id 
               AND empenho.idcliente = municipe.idcliente 
             LIMIT 1) as nome_municipe
        ")
            ->selectRaw("SUM(movimento.emissao) as empenhado")
            ->selectRaw("SUM(movimento.anular) as anulado")
            ->selectRaw("SUM(movimento.liquidacao) as liquidado")
            ->selectRaw("SUM(movimento.pagamento) as pago")
            ->selectRaw("SUM(movimento.emissao - movimento.anular) as saldo_empenhado")
            ->selectRaw("SUM(movimento.emissao - movimento.anular - movimento.liquidacao) as saldo_liquidar")
            ->selectRaw("SUM(movimento.liquidacao - movimento.pagamento) as saldo_pagar")
            ->where('movimento.idcliente', $idCliente)
            ->where('empenho.exercicio', $exercicio)
            ->where(function ($query) {
                $query->where('elemento.estrutural', 'like', '3.3.9.0.14.%')
                    ->orWhere('elemento.estrutural', 'like', '3.3.9.2.14.%')
                    ->orWhere('elemento.estrutural', 'like', '3.3.9.5.14.%')
                    ->orWhere('elemento.estrutural', 'like', '3.3.9.6.14.%')
                    ->orWhere('elemento.estrutural', 'like', '4.4.9.0.14.%');
            })
            ->groupBy(
                'movimento.idcliente',
                'empenho.idcliente', // <--- Adicionado para resolver o erro 1055
                'empenho.idcredor',
                'empenho.exercicio'
            );


        // 2. Wrap Externo
        $queryExterna = DB::table(DB::raw("({$subQuery->toSql()}) as tab_diarias"))
            ->mergeBindings($subQuery);

        // 3. CALCULANDO OS TOTAIS (Ajustado para evitar o erro 1140)
        $totais = DB::table(DB::raw("({$subQuery->toSql()}) as totais_gerais"))
            ->mergeBindings($subQuery)
            ->selectRaw("
                            SUM(empenhado) as total_empenhado,
                            SUM(anulado) as total_anulado,
                            SUM(liquidado) as total_liquidado,
                            SUM(pago) as total_pago,
                            SUM(saldo_empenhado) as total_saldo_empenhado,
                            SUM(saldo_liquidar) as total_saldo_liquidar,
                            SUM(saldo_pagar) as total_saldo_pagar
                        ")
            ->first(); // O first() aqui funciona pois o select só tem agregações (SUMs)

        // 4. Dados Paginados
        $data = $queryExterna->orderByDesc('saldo_empenhado')->paginate(25);

        return view('despesa.diarias.detalhe', compact('data', 'totais', 'exercicio'));
    }

    public function detalheCredor($exc, $cad)
    {
        $idCliente = env('CLIENT_ID');

        // 1. Dados do Credor (Bloco Azul/Topo)
        $credor = DB::table('cadmunicipe')
            ->where('idcliente', $idCliente)
            ->where('id', $cad)
            ->first();

        // 2. Query detalhada dos Empenhos (Bloco Verde/Tabela)
        $empenhos = DB::table('ctbempenhomovimento as movimento')
            ->join('ctbempenho as empenho', function ($join) {
                $join->on('empenho.id', '=', 'movimento.idempenho')
                    ->on('empenho.idcliente', '=', 'movimento.idcliente');
            })
            ->join('ctbcontadespesa as contaDespesa', function ($join) {
                $join->on('contaDespesa.id', '=', 'empenho.iddespesa')
                    ->on('contaDespesa.idcliente', '=', 'empenho.idcliente');
            })
            ->join('ctbelemento as elemento', function ($join) {
                $join->on('elemento.id', '=', 'contaDespesa.idelemento')
                    ->on('elemento.idcliente', '=', 'contaDespesa.idcliente');
            })
            ->select([
                'movimento.idcliente as cliente_id',
                'movimento.idempenho as empenho_id',
                'empenho.idcredor as credor_id',
                'empenho.exercicio',
                'empenho.dataemissao as data_emissao',
                'empenho.numero',
                'empenho.tipo',
            ])
            ->selectRaw("SUM(movimento.emissao) AS empenhado")
            ->selectRaw("SUM(movimento.anular) AS anulado")
            ->selectRaw("SUM(movimento.liquidacao) AS liquidado")
            ->selectRaw("SUM(movimento.pagamento) AS pago")
            ->selectRaw("SUM(movimento.emissao - movimento.anular) AS saldo_empenhado")
            ->selectRaw("SUM(movimento.emissao - movimento.anular - movimento.liquidacao) AS saldo_liquidar")
            ->selectRaw("SUM(movimento.liquidacao - movimento.pagamento) AS saldo_pagar")
            ->where('movimento.idcliente', $idCliente)
            ->where('empenho.exercicio', $exc)
            ->where('empenho.idcredor', $cad)
            ->where(function ($query) {
                $query->where('elemento.estrutural', 'like', '3.3.9.0.14.%')
                    ->orWhere('elemento.estrutural', 'like', '3.3.9.2.14.%')
                    ->orWhere('elemento.estrutural', 'like', '3.3.9.5.14.%')
                    ->orWhere('elemento.estrutural', 'like', '3.3.9.6.14.%')
                    ->orWhere('elemento.estrutural', 'like', '4.4.9.0.14.%');
            })
            ->groupBy(
                'movimento.idcliente',
                'movimento.idempenho',
                'empenho.idcredor',
                'empenho.exercicio',
                'empenho.dataemissao',
                'empenho.numero',
                'empenho.tipo'
            )
            ->orderByDesc('empenho.dataemissao')
            ->get();

        return view('despesa.diarias.credor_detalhe', compact('credor', 'empenhos', 'exc'));
    }

    public function detalheEmpenho($exc, $cad, $emp)
    {
        $idCliente = env('CLIENT_ID');

        // 1. Dados do Credor (Bloco Azul/Topo)
        $credor = DB::table('cadmunicipe')
            ->where('idcliente', $idCliente)
            ->where('id', $cad)
            ->first();

        // Query do Empenho (Identificação e Dotação) - Baseada no seu Empenho.sql
        $empenho = DB::table('ctbempenho as empenho')
            ->join('cadmunicipe as municipe', function ($j) {
                $j->on('municipe.id', '=', 'empenho.idcredor')->on('municipe.idcliente', '=', 'empenho.idcliente');
            })
            ->join('ctbcontadespesa as contaDespesa', function ($j) {
                $j->on('contaDespesa.id', '=', 'empenho.iddespesa')->on('contaDespesa.idcliente', '=', 'empenho.idcliente');
            })
            ->join('ctbunidadeorcamentaria as unidade', function ($j) {
                $j->on('unidade.id', '=', 'contaDespesa.idunidadeorcamentaria')->on('unidade.idcliente', '=', 'contaDespesa.idcliente');
            })
            ->select([
                'empenho.id',
                'empenho.numero',
                'empenho.dataemissao',
                'empenho.tipo',
                'municipe.nome as nome_municipe',
                'contaDespesa.codigo as codigo_despesa',
                'unidade.nome as unidade'
            ])
            ->selectRaw("IFNULL(municipe.cpf, municipe.cnpj) AS documento")
            // Subqueries baseadas no seu arquivo Empenho.sql
            ->selectRaw("(SELECT nome FROM ctbmodalidadelicitacao WHERE id = empenho.idlicitacao AND idcliente = empenho.idcliente) as modalidade")
            ->selectRaw("(SELECT nome FROM ctbespecieempenho WHERE id = empenho.idespecie AND idcliente = empenho.idcliente) as especie")
            ->selectRaw("(SELECT nome FROM ctbobjetivodespesa WHERE id = empenho.idobjetivo AND idcliente = empenho.idcliente) as objeto")
            ->selectRaw("(SELECT nome FROM ctborgao WHERE id = unidade.idorgao AND idcliente = unidade.idcliente) as orgao")
            ->selectRaw("(SELECT nome FROM ctbfuncao WHERE id = contaDespesa.idfuncao AND idcliente = contaDespesa.idcliente) as funcao")
            ->selectRaw("(SELECT nome FROM ctbsubfuncao WHERE id = contaDespesa.idsubfuncao AND idcliente = contaDespesa.idcliente) as sub_funcao")
            ->selectRaw("(SELECT nome FROM ctbprograma WHERE id = contaDespesa.idprograma AND idcliente = contaDespesa.idcliente) as programa")
            ->selectRaw("(SELECT nome FROM ctbsubprograma WHERE id = contaDespesa.idsubprograma AND idcliente = contaDespesa.idcliente) as sub_programa")
            ->selectRaw("(SELECT nome FROM ctbprojeto WHERE id = contaDespesa.idprojeto AND idcliente = contaDespesa.idcliente) as projeto")
            ->selectRaw("(SELECT CONCAT(estrutural, ' - ', nome) FROM ctbelemento WHERE id = contaDespesa.idelemento AND idcliente = contaDespesa.idcliente) as elemento")
            ->selectRaw("(SELECT nome FROM ctbunidadegestora WHERE id = contaDespesa.idgestora AND idcliente = contaDespesa.idcliente) as gestora")
            ->selectRaw("(SELECT nome FROM ctbrecursovinculado WHERE id = empenho.idrecurso AND idcliente = empenho.idcliente) as vinculo")
            ->where('empenho.idcliente', $idCliente)
            ->where('empenho.exercicio', $exc)
            ->where('empenho.id', $emp)
            ->first();

        // Query dos Itens - Baseada no seu Empenho item.sql
        $itens = DB::table('ctbempenhoitem')
            ->select([
                'id',
                'numero',
                'descricao',
                'quantidade',
                'valorunitario as valor_unitario', // Define o alias aqui
            ])
            ->selectRaw("(quantidade * valorunitario) AS valor_total") // Calcula o total conforme seu SQL
            ->where('idcliente', $idCliente)
            ->where('idempenho', $emp)
            ->get();

        return view('despesa.diarias.empenho_detalhe', compact('credor', 'empenho', 'itens', 'exc', 'cad'));
    }
}
