<?php

namespace App\Http\Controllers;

use App\Repositories\ReceitaRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReceitaController extends Controller
{
    private $idCliente;

    public function __construct(protected ReceitaRepository $repository)
    {
        
    }

    // --- ARRECADAÇÃO ELEMENTO ---
    public function arrecadacaoElementoIndex()
    {
        $resumoAnual = $this->repository->getResumoGeralPorExercicio(config('app.client_id'));
        return view('receita.arrecadacao.elemento.index', compact('resumoAnual'));
    }

    public function arrecadacaoElementoList($exercicio)
    {
        $dados = $this->repository->getArrecadadaPorElemento(config('app.client_id'), $exercicio);
        return view('receita.arrecadacao.elemento.list', compact('dados', 'exercicio'));
    }

    public function arrecadacaoRecursoIndex()
    {
        $resumoAnual = $this->repository->getResumoGeralPorExercicio(config('app.client_id'));
        return view('receita.arrecadacao.recurso.index', compact('resumoAnual'));
    }

    public function arrecadacaoRecursoList($exercicio)
    {
        $dados = $this->repository->getArrecadadaPorRecurso(config('app.client_id'), $exercicio);
        return view('receita.arrecadacao.recurso.list', compact('dados', 'exercicio'));
    }

    public function arrecadacaoRecursoDetails($exercicio, $recursoId)
    {
        $dados = $this->repository->getArrecadadaPorRecursoDetalhes(config('app.client_id'), $exercicio, $recursoId);

        $recurso = DB::table('ctbrecursovinculado')
            ->select('id', 'codigo', 'nome as descricao')
            ->where('id', $recursoId)
            ->where('exercicio', $exercicio)
            ->where('idcliente', config('app.client_id'))
            ->first();

        return view('receita.arrecadacao.recurso.details', compact('dados', 'exercicio', 'recurso'));
    }

    public function arrecadacaoElementoDetails($exercicio, $elementoId)
    {
        $dados = $this->repository->getArrecadadaPorElementoDetalhes(config('app.client_id'), $exercicio, $elementoId);

        $elemento = DB::table('ctbelemento')
            ->select('id', 'estrutural', 'nome as descricao')
            ->where('id', $elementoId)
            ->where('exercicio', $exercicio)
            ->where('idcliente', config('app.client_id'))
            ->first();

        return view('receita.arrecadacao.elemento.details', compact('dados', 'exercicio', 'elemento'));
    }

    // --- EXECUÇÃO RECURSO (Exemplo 2 níveis) ---
    public function execucaoRecursoIndex()
    {
        $resumoAnual = $this->repository->getResumoGeralPorExercicio(config('app.client_id'));
        return view('receita.execucao.recurso.index', compact('resumoAnual'));
    }

    public function execucaoRecursoList($exercicio)
    {
        $dados = $this->repository->getExecutadaPorRecurso(config('app.client_id'), $exercicio);
        return view('receita.execucao.recurso.list', compact('dados', 'exercicio'));
    }

    // --- EXECUÇÃO POR ELEMENTO (2 Níveis) ---
    public function execucaoElementoIndex()
    {
        // Usa o resumo geral para listar os anos
        $resumoAnual = $this->repository->getResumoGeralPorExercicio(config('app.client_id'));
        return view('receita.execucao.elemento.index', compact('resumoAnual'));
    }

    public function execucaoElementoList($exercicio)
    {
        $dados = $this->repository->getExecutadaPorElemento(config('app.client_id'), $exercicio);

        return view('receita.execucao.elemento.list', compact('dados', 'exercicio'));
    }
}
