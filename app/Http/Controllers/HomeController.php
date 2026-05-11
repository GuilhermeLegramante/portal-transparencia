<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        $id = config('app.client_id'); // ID do cliente
        $exc = date('Y'); // Exercício corrente

        // --- QUERY DE RECEITA ---
        $receita = DB::table('ctbcontareceita as receita')
            ->selectRaw("
                SUM((SELECT SUM(orcamento.total) FROM ctbcontareceitaloa orcamento 
                     WHERE orcamento.idreceita = receita.id AND orcamento.idcliente = receita.idcliente)) as valor_orcado,
                SUM((SELECT SUM(movimento.total) FROM ctbcontareceitamovimento movimento 
                     WHERE movimento.idreceita = receita.id AND movimento.idcliente = receita.idcliente)) as valor_executado
            ")
            ->where('receita.idcliente', $id)
            ->where('receita.exercicio', $exc)
            ->first();

        // --- QUERY DE DESPESA ---
        $despesa = DB::table('ctbcontadespesa as despesa')
            ->selectRaw("
                SUM((SELECT SUM(orcamento.total) FROM ctbcontadespesaloa orcamento 
                     WHERE orcamento.iddespesa = despesa.id AND orcamento.idcliente = despesa.idcliente)) as valor_orcado,
                SUM((SELECT SUM(IF(decreto.operacao = 'S', decreto.total, -decreto.total)) FROM ctbcontadespesaextra decreto 
                     WHERE decreto.iddespesa = despesa.id AND decreto.idcliente = despesa.idcliente)) as valor_corrigido,
                SUM((SELECT SUM(movimento.emissao - movimento.anular) FROM ctbempenhomovimento movimento 
                     INNER JOIN ctbempenho empenho ON empenho.id = movimento.idempenho AND empenho.idcliente = movimento.idcliente
                     WHERE empenho.iddespesa = despesa.id AND movimento.idcliente = despesa.idcliente AND empenho.tipo = 'O')) as valor_executado
            ")
            ->where('despesa.idcliente', $id)
            ->where('despesa.exercicio', $exc)
            ->first();

        // --- CÁLCULOS DOS CARDS ---
        $dados = [
            'gasto_previsto'    => ($despesa->valor_orcado ?? 0) + ($despesa->valor_corrigido ?? 0),
            'gasto_executado'   => $despesa->valor_executado ?? 0,
            'perc_comprometido' => 0,
            'repasse_leg'       => ($receita->valor_orcado ?? 0) - ($despesa->valor_orcado ?? 0),
            'arrecadacao_prev'  => $receita->valor_orcado ?? 0,
            'arrecadacao_real'  => $receita->valor_executado ?? 0,
            'perc_receita'      => 0,
            'deficit_orc'       => ($receita->valor_executado ?? 0) - ($despesa->valor_executado ?? 0)
        ];

        // Cálculos de porcentagem
        if ($despesa->valor_orcado > 0) {
            // Percentual de comprometimento (%) = (valor_executado / valor_orcado) * 100
            $dados['perc_comprometido'] = ($despesa->valor_executado / $despesa->valor_orcado) * 100;
        }

        if ($dados['arrecadacao_prev'] > 0) {
            $dados['perc_receita'] = ($dados['arrecadacao_real'] / $dados['arrecadacao_prev']) * 100;
        }

        return view('home', compact('dados'));
    }
}
