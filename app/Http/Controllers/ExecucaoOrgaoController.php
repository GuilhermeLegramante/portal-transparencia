<?php

namespace App\Http\Controllers;

use App\Repositories\ExecucaoRepository;
use App\Repositories\MunicipeRepository;
use Illuminate\Http\Request;

class ExecucaoOrgaoController extends Controller
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

        return view('despesa.execucao_orgao.index', compact('resumoAnual'));
    }

    public function list($exercicio)
    {
        $data = $this->execucaoRepo->getExecucaoPorOrgao($exercicio, config('app.client_id'));

        return view('despesa.execucao_orgao.list', compact('data', 'exercicio'));
    }
}
