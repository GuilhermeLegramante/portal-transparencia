<?php

namespace App\Http\Controllers;

use App\Repositories\ExecucaoRepository;
use App\Repositories\MunicipeRepository;
use Illuminate\Http\Request;

class ExecucaoElementoController extends Controller
{
    private $idCliente;

    protected $municipeRepo;
    protected $execucaoRepo;

    public function __construct(MunicipeRepository $municipeRepo, ExecucaoRepository $execucaoRepo)
    {
        $this->municipeRepo = $municipeRepo;
        $this->execucaoRepo = $execucaoRepo;

        $this->idCliente = config('app.client_id');
    }

    public function index()
    {
        $resumoAnual = $this->execucaoRepo->getResumoGeralPorExercicio($this->idCliente);

        return view('despesa.execucao_elemento.index', compact('resumoAnual'));
    }

     public function list($exercicio)
    {
        $elementos = $this->execucaoRepo->getExecucaoPorElemento($exercicio, $this->idCliente);

        return view('despesa.execucao_elemento.list', compact('elementos', 'exercicio'));
    }
}
