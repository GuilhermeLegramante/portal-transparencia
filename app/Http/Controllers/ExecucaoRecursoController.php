<?php

namespace App\Http\Controllers;

use App\Repositories\ExecucaoRepository;
use App\Repositories\MunicipeRepository;
use Illuminate\Http\Request;

class ExecucaoRecursoController extends Controller
{
    private $idCliente;

    protected $municipeRepo;
    protected $execucaoRepo;

    public function __construct(MunicipeRepository $municipeRepo, ExecucaoRepository $execucaoRepo)
    {
        $this->municipeRepo = $municipeRepo;
        $this->execucaoRepo = $execucaoRepo;

        
    }

    public function index()
    {
        $resumoAnual = $this->execucaoRepo->getResumoGeralPorExercicio(config('app.client_id'));

        return view('despesa.execucao_recurso.index', compact('resumoAnual'));
    }

    public function list($exercicio)
    {
        $data = $this->execucaoRepo->getExecucaoPorRecurso($exercicio, config('app.client_id'));

        return view('despesa.execucao_recurso.list', compact('data', 'exercicio'));
    }
}
