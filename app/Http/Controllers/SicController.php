<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SicController extends Controller
{
    private $idCliente;

    public function __construct()
    {
        // O idCliente já vem configurado pelo seu TenantMiddleware
        $this->idCliente = config('app.client_id');
    }

    public function index()
    {
        $breadcrumb = [
            'Início' => route('home'),
            'SIC - Serviço de Informação ao Cidadão' => ''
        ];

        // Dados dinâmicos para a página inicial (Exemplo de estatística rápida)
        $totalPedidos = DB::table('sic_pedidos') // Ajuste o nome da sua tabela de pedidos
            ->where('idcliente', $this->idCliente)
            ->count();

        return view('sic.index', compact('breadcrumb', 'totalPedidos'));
    }

    public function contato()
    {
        $breadcrumb = [
            'SIC' => route('sic.index'),
            'Fale Conosco' => ''
        ];
        return view('sic.contato', compact('breadcrumb'));
    }

    public function estatisticas(Request $request)
    {
        $exercicio = $request->get('exercicio', date('Y'));

        $breadcrumb = [
            'SIC' => route('sic.index'),
            'Consulta de Solicitações' => ''
        ];

        $pedidos = DB::table('sicpedido as pedido')
            ->join('sicusuario as usuario', function ($join) {
                $join->on('usuario.id', '=', 'pedido.idusuario')
                    ->on('pedido.idcliente', '=', 'usuario.idcliente');
            })
            ->select(
                'pedido.datahora',
                'usuario.nome as solicitante',
                'usuario.tipopessoa',
                'pedido.titulo as descricao',
                'pedido.situacao',
                'pedido.avaliacao'
            )
            ->where('pedido.idcliente', $this->idCliente)
            ->whereYear('pedido.datahora', $exercicio)
            ->orderBy('pedido.datahora', 'desc')
            ->get();

        return view('sic.estatisticas', compact('pedidos', 'exercicio', 'breadcrumb'));
    }
}
