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

        $resumoUnidades = $this->repo->getResumoPorUnidade($idcliente, $exercicio);
        $resumoSubfuncoes = $this->repo->getResumoPorSubfuncao($idcliente, $exercicio);
        $resumoElementos = $this->repo->getResumoPorElemento($idcliente, $exercicio);
        $resumoRecursos = $this->repo->getResumoPorRecurso($idcliente, $exercicio);
        $resumoFuncoes = $this->repo->getResumoPorFuncao($idcliente, $exercicio);

        // Dados para o Gráfico de BI: Top 5 Unidades que mais empenharam no ano
        $topUnidades = collect($resumoUnidades)
            ->sortByDesc('valor_empenhado_exercicio')
            ->take(5)
            ->values();

        // Dados para o Gráfico de BI: Maiores Funções (Composição do Orçamento)
        $biFuncoes = collect($resumoFuncoes)
            ->sortByDesc('valor_atualizado_exercicio')
            ->values();

        $funcoes = collect($resumoFuncoes);

        /*
        |--------------------------------------------------------------------------
        | Maior crescimento
        |--------------------------------------------------------------------------
        */
        $maiorCrescimento = $funcoes->sortByDesc('variacao_gastos')->first();

        /*
        |--------------------------------------------------------------------------
        | Maior comprometimento
        |--------------------------------------------------------------------------
        */
        $maisComprometida = $funcoes
            ->map(function ($item) {
                $item->percentual_comprometido =
                    $item->valor_atualizado_exercicio > 0
                    ? ($item->valor_empenhado_exercicio / $item->valor_atualizado_exercicio) * 100
                    : 0;

                return $item;
            })
            ->sortByDesc('percentual_comprometido')
            ->first();

        /*
        |--------------------------------------------------------------------------
        | Melhor execução financeira
        |--------------------------------------------------------------------------
        */
        $melhorExecucao = $funcoes
            ->map(function ($item) {
                $item->indice_execucao =
                    $item->valor_empenhado_exercicio > 0
                    ? ($item->valor_pago_exercicio / $item->valor_empenhado_exercicio) * 100
                    : 0;

                return $item;
            })
            ->sortByDesc('indice_execucao')
            ->first();

        /*
        |--------------------------------------------------------------------------
        | Pior execução financeira
        |--------------------------------------------------------------------------
        */
        $piorExecucao = $funcoes
            ->map(function ($item) {
                $item->indice_execucao =
                    $item->valor_empenhado_exercicio > 0
                    ? ($item->valor_pago_exercicio / $item->valor_empenhado_exercicio) * 100
                    : 0;

                return $item;
            })
            ->sortBy('indice_execucao')
            ->first();

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
            'maiorCrescimento',
            'maisComprometida',
            'melhorExecucao',
            'piorExecucao'
        ));
    }
}
