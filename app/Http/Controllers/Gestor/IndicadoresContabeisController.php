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

        return view('gestor.indicadores.index', compact(
            'exercicio',
            'resumoAnual',
            'pctComprometidoAnterior',
            'pctComprometidoExercicio',
            'labelsGrafico',
            'empenhadoExercicio',
            'empenhadoAnterior',
            'breadcrumb'
        ));
    }
}
