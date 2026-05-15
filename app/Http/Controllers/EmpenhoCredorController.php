<?php

namespace App\Http\Controllers;

use App\Repositories\EmpenhoRepository;
use App\Repositories\MunicipeRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmpenhoCredorController extends Controller
{
    private $idCliente;

    protected $municipeRepo;
    protected $empenhoRepo;

    public function __construct(MunicipeRepository $municipeRepo, EmpenhoRepository $empenhoRepo)
    {
        $this->municipeRepo = $municipeRepo;
        $this->empenhoRepo = $empenhoRepo;

        $this->idCliente = config('app.client_id');
    }

    /**
     * Lista o resumo de empenhos agrupados por exercício
     */
    public function index()
    {
        dd($this->idCliente);
        $resumoAnual = $this->empenhoRepo->resumoAnualPorExercicio($this->idCliente);

        return view('despesa.empenho_credor.index', compact('resumoAnual'));
    }

    /**
     * Lista os credores e seus respectivos totais dentro de um exercício específico
     */
    public function listaCredores($exercicio)
    {
        $credores = DB::table('ctbempenhomovimento as m')
            ->join('ctbempenho as e', function ($join) {
                $join->on('e.id', '=', 'm.idempenho')
                    ->on('e.idcliente', '=', 'm.idcliente');
            })
            // Substituímos o sub-select por um Left Join para melhor performance
            ->leftJoin('cadmunicipe as mun', function ($join) {
                $join->on('mun.id', '=', 'e.idcredor')
                    ->on('mun.idcliente', '=', 'e.idcliente');
            })
            ->where('m.idcliente', $this->idCliente)
            ->where('e.exercicio', $exercicio)
            ->select(
                'e.idcredor as credor_id',
                'mun.nome',
                DB::raw('SUM(m.emissao - m.anular) as total_empenhado'),
                DB::raw('SUM(m.liquidacao) as total_liquidado'),
                DB::raw('SUM(m.pagamento) as total_pago'),
                DB::raw('SUM(m.liquidacao - m.pagamento) as saldo_pagar')
            )
            ->groupBy('e.idcredor', 'mun.nome', 'e.idcliente', 'e.exercicio')
            ->orderBy('mun.nome', 'asc')
            ->get();

        return view('despesa.empenho_credor.lista', compact('credores', 'exercicio'));
    }

    /**
     * Detalha os dados do credor e lista todos os seus empenhos no exercício
     */
    public function detalhes($exercicio, $credor_id)
    {
        $credor = $this->municipeRepo->findById($credor_id, $this->idCliente);

        // Se não encontrar o credor, você pode redirecionar ou dar erro 404
        if (!$credor) {
            abort(404, 'Credor não encontrado.');
        }

        // Busca lista de empenhos do credor no exercício
        $empenhos = DB::table('ctbempenhomovimento as m')
            ->join('ctbempenho as e', function ($join) {
                $join->on('e.id', '=', 'm.idempenho')
                    ->on('e.idcliente', '=', 'm.idcliente');
            })
            ->where('m.idcliente', $this->idCliente)
            ->where('e.exercicio', $exercicio)
            ->where('e.idcredor', $credor_id)
            ->select(
                'e.id',
                'e.numero',
                'e.tipo',
                'e.idcredor AS credor_id',
                'e.dataemissao as data_emissao',
                DB::raw('SUM(m.emissao - m.anular) as saldo_empenhado'),
                DB::raw('SUM(m.liquidacao) as saldo_liquidado'),
                DB::raw('SUM(m.pagamento) as saldo_pago'),
                DB::raw('SUM(m.liquidacao - m.pagamento) as saldo_pagar')
            )
            ->groupBy('m.idempenho', 'e.numero', 'e.tipo', 'e.dataemissao', 'e.idcredor', 'e.id')
            ->orderBy('e.dataemissao', 'desc')
            ->get();

        return view('despesa.empenho_credor.detalhe', compact('credor', 'empenhos', 'exercicio'));
    }

    public function detalheEmpenho($exercicio, $credor_id, $empenho_id)
    {
        $credor = $this->municipeRepo->findById($credor_id, $this->idCliente);

        $empenho = $this->empenhoRepo->findById($empenho_id, $exercicio, $this->idCliente);

        $itens = $this->empenhoRepo->findItemsByEmpenhoId($empenho_id, $this->idCliente);

        return view('despesa.empenho_credor.detalhe-empenho', compact('credor', 'empenho', 'itens', 'exercicio', 'credor_id'));
    }
}
