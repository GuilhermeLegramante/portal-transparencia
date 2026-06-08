<?php

namespace App\Http\Controllers\Gestor;

use App\Http\Controllers\Controller;
use App\Repositories\IndicadoresContabeisRepository;
use Illuminate\Http\Request;

class IndicadoresContabeisController extends Controller
{
    protected $repo;

    public function __construct(IndicadoresContabeisRepository $repo)
    {
        $this->repo = $repo;
    }

    public function index(Request $request)
    {
        $idcliente = config('app.client_id');
        $exercicio = $request->input('exercicio', date('Y'));

        // Busca dados do banco
        $resumoAnual = $this->repo->getResumoGeralDespesa($idcliente, $exercicio);
        $dadosMensais = $this->repo->getEvolucaoMensalDespesa($idcliente, $exercicio);

        // Lógica de cálculo dos percentuais comprometidos (Evitando divisão por zero)
        $pctComprometidoAnterior = 0;
        $pctComprometidoExercicio = 0;

        if ($resumoAnual) {
            if ($resumoAnual->valor_atualizado_anterior > 0) {
                $pctComprometidoAnterior = ($resumoAnual->valor_empenhado_anterior / $resumoAnual->valor_atualizado_anterior) * 100;
            }
            if ($resumoAnual->valor_atualizado_exercicio > 0) {
                $pctComprometidoExercicio = ($resumoAnual->valor_empenhado_exercicio / $resumoAnual->valor_atualizado_exercicio) * 100;
            }
        }

        // Mapeamento de meses para nomenclatura legível no gráfico
        $nomesMeses = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
        $labelsGrafico = [];
        $empenhadoExercicio = [];
        $empenhadoAnterior = [];

        foreach ($dadosMensais as $reg) {
            $labelsGrafico[] = $nomesMeses[$reg->mes - 1] ?? "Mês " . $reg->mes;
            $empenhadoExercicio[] = (float) $reg->valor_empenhado_exercicio;
            $empenhadoAnterior[] = (float) $reg->valor_empenhado_anterior;
        }

        $breadcrumb = [
            'Painel do Gestor' => '',
            'Indicadores Contábeis' => ''
        ];

        // --- CARREGA OS NOVOS DADOS ANALÍTICOS (MENSALIZADOS) ---
        $unidadesMensal = collect($this->repo->getResumoUnidades($idcliente, $exercicio));
        $funcoesMensal  = collect($this->repo->getResumoFuncoes($idcliente, $exercicio));
        $subfuncoesMensal = collect($this->repo->getResumoSubfuncoes($idcliente, $exercicio));
        $elementosMensal = collect($this->repo->getResumoElementos($idcliente, $exercicio));
        $recursosMensal  = collect($this->repo->getResumoRecursos($idcliente, $exercicio));

        // --- CONSOLIDAÇÃO DOS DADOS PARA AS ABAS DA VIEW ---

        // 1. Unidades Orçamentárias
        $resumoUnidades = $unidadesMensal->groupBy('codigo')->map(function ($items) {
            return (object) [
                'codigo' => $items->first()->codigo,
                'mes' => $items->first()->mes,
                'descricao' => $items->first()->descricao,
                'valor_empenhado_anterior' => $items->sum('valor_empenhado_anterior'),
                'valor_empenhado_exercicio' => $items->sum('valor_empenhado_exercicio'),
                'valor_pago_anterior' => $items->sum('valor_pago_anterior'),
                'valor_pago_exercicio' => $items->sum('valor_pago_exercicio'),
            ];
        })->values();

        // 2. Funções
        $resumoFuncoes = $funcoesMensal->groupBy('codigo')->map(function ($items) {
            return (object) [
                'codigo' => $items->first()->codigo,
                'mes' => $items->first()->mes,
                'descricao' => $items->first()->descricao,
                'valor_emissao_anterior' => $items->sum('valor_emissao_anterior'),
                'valor_emissao_exercicio' => $items->sum('valor_emissao_exercicio'),
                'valor_pago_anterior' => $items->sum('valor_pago_anterior'),
                'valor_pago_exercicio' => $items->sum('valor_pago_exercicio'),
            ];
        })->values();

        // 3. Subfunções
        $resumoSubfuncoes = $subfuncoesMensal->groupBy('codigo')->map(function ($items) {
            return (object) [
                'codigo' => $items->first()->codigo,
                'mes' => $items->first()->mes,
                'descricao' => $items->first()->descricao,
                'valor_emissao_anterior' => $items->sum('valor_emissao_anterior'),
                'valor_emissao_exercicio' => $items->sum('valor_emissao_exercicio'),
                'valor_pago_anterior' => $items->sum('valor_pago_anterior'),
                'valor_pago_exercicio' => $items->sum('valor_pago_exercicio'),
            ];
        })->values();

        // 4. Elementos de Despesa
        $resumoElementos = $elementosMensal->groupBy('estrutural')->map(function ($items) {
            return (object) [
                'estrutural' => $items->first()->estrutural,
                'mes' => $items->first()->mes,
                'descricao' => $items->first()->descricao,
                'valor_emissao_anterior' => $items->sum('valor_emissao_anterior'),
                'valor_emissao_exercicio' => $items->sum('valor_emissao_exercicio'),
                'valor_pago_anterior' => $items->sum('valor_pago_anterior'),
                'valor_pago_exercicio' => $items->sum('valor_pago_exercicio'),
            ];
        })->values();

        // 5. Recursos Vinculados
        $resumoRecursos = $recursosMensal->groupBy('codigo')->map(function ($items) {
            return (object) [
                'codigo' => $items->first()->codigo,
                'mes' => $items->first()->mes,
                'descricao' => $items->first()->descricao,
                'valor_emissao_anterior' => $items->sum('valor_emissao_anterior'),
                'valor_emissao_exercicio' => $items->sum('valor_emissao_exercicio'),
                'valor_pago_anterior' => $items->sum('valor_pago_anterior'),
                'valor_pago_exercicio' => $items->sum('valor_pago_exercicio'),
            ];
        })->values();

        return view('gestor.indicadores.index', compact(
            'exercicio',
            'resumoAnual',
            'pctComprometidoAnterior',
            'pctComprometidoExercicio',
            'labelsGrafico',
            'empenhadoExercicio',
            'empenhadoAnterior',
            'breadcrumb',
            'resumoUnidades',
            'resumoFuncoes',
            'resumoSubfuncoes',
            'resumoElementos',
            'resumoRecursos'
        ));
    }
}
