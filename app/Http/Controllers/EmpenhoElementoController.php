<?php

namespace App\Http\Controllers;

use App\Repositories\EmpenhoRepository;
use App\Repositories\MunicipeRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmpenhoElementoController extends Controller
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
        $resumoAnual = $this->empenhoRepo->resumoAnualPorExercicio($this->idCliente);

        return view('despesa.empenho_elemento.index', compact('resumoAnual'));
    }

    /**
     * Lista os elementos e seus respectivos totais dentro de um exercício específico
     */
    public function listaElementos($exercicio)
    {
        $elementos = $this->empenhoRepo->listaElementos($exercicio, $this->idCliente);

        return view('despesa.empenho_elemento.lista', compact('elementos', 'exercicio'));
    }

    public function detalhes($exercicio, $elemento_id)
    {
        $elemento = $this->empenhoRepo->findElementoDespesaById(
            (int)$elemento_id,
            (int)$exercicio,
            config('app.client_id')
        );

        $empenhos = $this->empenhoRepo->findEmpenhosByElemento(
            (int)$elemento_id,
            (int)$exercicio,
            config('app.client_id')
        );

        $columns = [
            ['label' => '', 'icone' => 'search', 'align' => 'text-center'],
            ['label' => 'Número', 'icone' => 'hashtag', 'align' => 'text-center'],
            ['label' => 'Emissão', 'icone' => 'calendar', 'align' => 'text-center'],
            ['label' => 'Credor', 'icone' => 'user', 'align' => 'text-start'],
            ['label' => 'Empenhado', 'icone' => '', 'align' => 'text-end'],
            ['label' => 'Liquidado', 'icone' => '', 'align' => 'text-end'],
            ['label' => 'Pago', 'icone' => '', 'align' => 'text-end'],
            ['label' => 'Saldo a Pagar', 'icone' => '', 'align' => 'text-end'],
        ];

        return view('despesa.empenho_elemento.detalhes', compact('empenhos', 'exercicio', 'columns', 'elemento'));
    }

    public function detalheEmpenho($exercicio, $elemento_id, $empenho_id)
    {
        $elemento = $this->empenhoRepo->findElementoDespesaById(
            (int)$elemento_id,
            (int)$exercicio,
            config('app.client_id')
        );

        $empenho = $this->empenhoRepo->findById($empenho_id, $exercicio, $this->idCliente);

        $itens = $this->empenhoRepo->findItemsByEmpenhoId($empenho_id, $this->idCliente);

        return view('despesa.empenho_elemento.detalhe-empenho', compact('elemento', 'empenho', 'itens', 'exercicio', 'elemento_id'));
    }
}
