<?php

namespace App\Http\Controllers;

use App\Repositories\EmpenhoRepository;
use App\Repositories\MunicipeRepository;
use Illuminate\Http\Request;

class EmpenhoRecursoController extends Controller
{
     private $idCliente;

    protected $municipeRepo;
    protected $empenhoRepo;

    public function __construct(MunicipeRepository $municipeRepo, EmpenhoRepository $empenhoRepo)
    {
        $this->municipeRepo = $municipeRepo;
        $this->empenhoRepo = $empenhoRepo;

        
    }

    /**
     * Lista o resumo de empenhos agrupados por exercício
     */
    public function index()
    {
        $resumoAnual = $this->empenhoRepo->resumoAnualPorExercicio(config('app.client_id'));

        return view('despesa.empenho_recurso.index', compact('resumoAnual'));
    }

    /**
     * Lista os recursos e seus respectivos totais dentro de um exercício específico
     */
    public function listaRecursos($exercicio)
    {
        $recursos = $this->empenhoRepo->listaRecursos($exercicio, config('app.client_id'));

        return view('despesa.empenho_recurso.lista', compact('recursos', 'exercicio'));
    }

    public function detalhes($exercicio, $recurso_id)
    {
        // 1. Busca os dados do Recurso para o Card de Identificação
        $recurso = $this->empenhoRepo->findRecursoById((int)$recurso_id, (int)$exercicio, config('app.client_id'));

        if (!$recurso) {
            abort(404, 'Recurso não encontrado');
        }

        // 2. Busca a lista de empenhos vinculados a este recurso
        $empenhos = $this->empenhoRepo->findEmpenhosByRecurso((int)$recurso_id, (int)$exercicio, config('app.client_id'));

        // 3. Define as colunas (conforme a imagem que você enviou)
        $columns = [
            ['label' => '', 'icone' => 'search', 'align' => 'text-center'],
            ['label' => 'Número', 'icone' => '', 'align' => 'text-center'],
            ['label' => 'Emissão', 'icone' => '', 'align' => 'text-center'],
            ['label' => 'Saldo empenhado', 'icone' => '', 'align' => 'text-end'],
            ['label' => 'Saldo liquidar', 'icone' => '', 'align' => 'text-end'],
            ['label' => 'Saldo pagar', 'icone' => '', 'align' => 'text-end'],
        ];

        return view('despesa.empenho_recurso.detalhes', compact('recurso', 'empenhos', 'exercicio', 'columns'));
    }

    public function detalheEmpenho($exercicio, $recurso_id, $empenho_id)
    {
        $recurso = $this->empenhoRepo->findRecursoById(
            (int)$recurso_id,
            (int)$exercicio,
            config('app.client_id')
        );

        $empenho = $this->empenhoRepo->findById($empenho_id, $exercicio, config('app.client_id'));

        $itens = $this->empenhoRepo->findItemsByEmpenhoId($empenho_id, config('app.client_id'));

        return view('despesa.empenho_recurso.detalhe-empenho', compact('recurso', 'empenho', 'itens', 'exercicio', 'recurso_id'));
    }
}
