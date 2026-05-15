<?php

namespace App\Http\Controllers;

use App\Repositories\EmpenhoRepository;
use App\Repositories\MunicipeRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DespesaController extends Controller
{
    private $idCliente;

    protected $municipeRepo;
    protected $empenhoRepo;

    public function __construct(MunicipeRepository $municipeRepo, EmpenhoRepository $empenhoRepo)
    {
        $this->municipeRepo = $municipeRepo;
        $this->empenhoRepo = $empenhoRepo;

        
    }

    public function resumoAnualDiarias(Request $request)
    {
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
            ->where('movimento.idcliente', config('app.client_id'))
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
            ->where('movimento.idcliente', config('app.client_id'))
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
        $data = $queryExterna->orderByDesc('saldo_empenhado')
            // ->paginate(25); por causa do datatables vou trazer todos os dados e paginar no frontend
            ->get();

        return view('despesa.diarias.detalhe', compact('data', 'totais', 'exercicio'));
    }

    public function detalheCredor( $exercicio, $credor_id)
    {
        $credor = $this->municipeRepo->findById($credor_id, config('app.client_id'));

        $empenhos = $this->empenhoRepo->findAllByCredorId($credor_id,  $exercicio, config('app.client_id'));

        return view('despesa.diarias.credor_detalhe', compact('credor', 'empenhos', 'exercicio'));
    }

    public function detalheEmpenho( $exercicio, $credor_id, $emp)
    {
        $credor = $this->municipeRepo->findById($credor_id, config('app.client_id'));

        $empenho = $this->empenhoRepo->findById($emp,  $exercicio, config('app.client_id'));

        $itens = $this->empenhoRepo->findItemsByEmpenhoId($emp, config('app.client_id'));

        return view('despesa.diarias.empenho_detalhe', compact('credor', 'empenho', 'itens', 'exercicio', 'credor_id'));
    }
}
