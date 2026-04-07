<?php

namespace App\Http\Controllers;

use App\Repositories\ExecucaoRepository;
use App\Repositories\MunicipeRepository;
use Illuminate\Http\Request;

class ExecucaoLocalizadorController extends Controller
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

        return view('despesa.execucao_localizador.index', compact('resumoAnual'));
    }

    public function list($exercicio)
    {
        $data = $this->execucaoRepo->getExecucaoPorLocalizador($exercicio, $this->idCliente);

        return view('despesa.execucao_localizador.list', compact('data', 'exercicio'));
    }
}
