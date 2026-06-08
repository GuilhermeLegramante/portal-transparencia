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

        // Mapeamento de meses para nomenclatura legível
        $nomesMeses = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
        $labelsGrafico = [];
        $empenhadoExercicio = [];
        $empenhadoAnterior = [];

        foreach ($dadosMensais as $reg) {
            $mesNum = isset($reg->mes) ? (int)$reg->mes : 1;
            $labelsGrafico[] = $nomesMeses[$mesNum - 1] ?? "Mês " . $mesNum;
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

        // 1. Unidades Orçamentárias (Ordenadas para o TOP 5 do BI)
        $resumoUnidades = $unidadesMensal->groupBy('codigo')->map(function ($items) use ($nomesMeses) {
            $primeiro = $items->first();
            $mesNum = isset($primeiro->mes) ? (int)$primeiro->mes : null;
            return (object) [
                'codigo' => $primeiro->codigo ?? '--',
                'mes' => $mesNum ? ($nomesMeses[$mesNum - 1] ?? str_pad($mesNum, 2, '0', STR_PAD_LEFT)) : '--',
                'descricao' => $primeiro->descricao ?? '--',
                'valor_empenhado_anterior' => $items->sum('valor_empenhado_anterior'),
                'valor_empenhado_exercicio' => $items->sum('valor_empenhado_exercicio'),
                'valor_pago_anterior' => $items->sum('valor_pago_anterior'),
                'valor_pago_exercicio' => $items->sum('valor_pago_exercicio'),
            ];
        })->values();

        // Dados para o Gráfico de BI: Top 5 Unidades que mais empenharam no ano
        $topUnidades = collect($resumoUnidades)
            ->sortByDesc('valor_empenhado_exercicio')
            ->take(5)
            ->values();


        // 2. Funções (Ordenadas para a Rosca do BI)
        $resumoFuncoes = $funcoesMensal->groupBy('codigo')->map(function ($items) use ($nomesMeses) {
            $primeiro = $items->first();
            $mesNum = isset($primeiro->mes) ? (int)$primeiro->mes : null;
            return (object) [
                'codigo' => $primeiro->codigo ?? '--',
                'mes' => $mesNum ? ($nomesMeses[$mesNum - 1] ?? str_pad($mesNum, 2, '0', STR_PAD_LEFT)) : '--',
                'descricao' => $primeiro->descricao ?? '--',
                'valor_emissao_anterior' => $items->sum('valor_emissao_anterior'),
                'valor_emissao_exercicio' => $items->sum('valor_emissao_exercicio'),
                'valor_pago_anterior' => $items->sum('valor_pago_anterior'),
                'valor_pago_exercicio' => $items->sum('valor_pago_exercicio'),
            ];
        })->values();

        // Dados para o Gráfico de BI: Maiores Funções (Composição do Orçamento)
        $biFuncoes = collect($resumoFuncoes)
            ->sortByDesc('valor_emissao_exercicio')
            ->values();

        // 3. Subfunções
        $resumoSubfuncoes = $subfuncoesMensal->groupBy('codigo')->map(function ($items) use ($nomesMeses) {
            $primeiro = $items->first();
            $mesNum = isset($primeiro->mes) ? (int)$primeiro->mes : null;
            return (object) [
                'codigo' => $primeiro->codigo ?? '--',
                'mes' => $mesNum ? ($nomesMeses[$mesNum - 1] ?? str_pad($mesNum, 2, '0', STR_PAD_LEFT)) : '--',
                'descricao' => $primeiro->descricao ?? '--',
                'valor_emissao_anterior' => $items->sum('valor_emissao_anterior'),
                'valor_emissao_exercicio' => $items->sum('valor_emissao_exercicio'),
                'valor_pago_anterior' => $items->sum('valor_pago_anterior'),
                'valor_pago_exercicio' => $items->sum('valor_pago_exercicio'),
            ];
        })->values();

        // 4. Elementos de Despesa
        $resumoElementos = $elementosMensal->groupBy('estrutural')->map(function ($items) use ($nomesMeses) {
            $primeiro = $items->first();
            $mesNum = isset($primeiro->mes) ? (int)$primeiro->mes : null;
            return (object) [
                'estrutural' => $primeiro->estrutural ?? '--',
                'mes' => $mesNum ? ($nomesMeses[$mesNum - 1] ?? str_pad($mesNum, 2, '0', STR_PAD_LEFT)) : '--',
                'descricao' => $primeiro->descricao ?? '--',
                'valor_emissao_anterior' => $items->sum('valor_emissao_anterior'),
                'valor_emissao_exercicio' => $items->sum('valor_emissao_exercicio'),
                'valor_pago_anterior' => $items->sum('valor_pago_anterior'),
                'valor_pago_exercicio' => $items->sum('valor_pago_exercicio'),
            ];
        })->values();

        // 5. Recursos Vinculados
        $resumoRecursos = $recursosMensal->groupBy('codigo')->map(function ($items) use ($nomesMeses) {
            $primeiro = $items->first();
            $mesNum = isset($primeiro->mes) ? (int)$primeiro->mes : null;
            return (object) [
                'codigo' => $primeiro->codigo ?? '--',
                'mes' => $mesNum ? ($nomesMeses[$mesNum - 1] ?? str_pad($mesNum, 2, '0', STR_PAD_LEFT)) : '--',
                'descricao' => $primeiro->descricao ?? '--',
                'valor_emissao_anterior' => $items->sum('valor_emissao_anterior'),
                'valor_emissao_exercicio' => $items->sum('valor_emissao_exercicio'),
                'valor_pago_anterior' => $items->sum('valor_pago_anterior'),
                'valor_pago_exercicio' => $items->sum('valor_pago_exercicio'),
            ];
        })->values();


        $dadosUnidade = $this->repo->getResumoPorUnidade($idcliente, $exercicio);
        $resumoRecursos = $this->repo->getResumoPorRecurso($idcliente, $exercicio);
        $resumoElementos = $this->repo->getResumoPorElemento($idcliente, $exercicio);
        $resumoSubfuncoes = $this->repo->getResumoPorSubfuncao($idcliente, $exercicio);
        $resumoFuncoes = $this->repo->getResumoPorFuncao($idcliente, $exercicio);

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
            'resumoRecursos',
            'topUnidades',
            'biFuncoes',
            'dadosUnidade',
            'resumoRecursos',
            'resumoElementos',
            'resumoSubfuncoes',
            'resumoFuncoes'
        ));
    }
}
