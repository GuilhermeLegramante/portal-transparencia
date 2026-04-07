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
        $this->idCliente = config('app.client_id');
    }

    // --- ARRECADAÇÃO ELEMENTO ---
    public function arrecadacaoElementoIndex()
    {
        $resumoAnual = $this->repository->getResumoGeralPorExercicio($this->idCliente);
        return view('receita.arrecadacao.elemento.index', compact('resumoAnual'));
    }

    public function arrecadacaoElementoList($exercicio)
    {
        $dados = $this->repository->getArrecadadaPorElemento($this->idCliente, $exercicio);
        return view('receita.arrecadacao.elemento.list', compact('dados', 'exercicio'));
    }

    public function arrecadacaoRecursoIndex()
    {
        $resumoAnual = $this->repository->getResumoGeralPorExercicio($this->idCliente);
        return view('receita.arrecadacao.recurso.index', compact('resumoAnual'));
    }

    public function arrecadacaoRecursoList($exercicio)
    {
        $dados = $this->repository->getArrecadadaPorRecurso($this->idCliente, $exercicio);
        return view('receita.arrecadacao.recurso.list', compact('dados', 'exercicio'));
    }

    public function arrecadacaoRecursoDetails($exercicio, $recursoId)
    {
        $dados = $this->repository->getArrecadadaPorRecursoDetalhes($this->idCliente, $exercicio, $recursoId);

        $recurso = DB::table('ctbrecursovinculado')
            ->select('id', 'codigo', 'nome as descricao')
            ->where('id', $recursoId)
            ->where('exercicio', $exercicio)
            ->where('idcliente', $this->idCliente)
            ->first();

        return view('receita.arrecadacao.recurso.details', compact('dados', 'exercicio', 'recurso'));
    }

    public function arrecadacaoElementoDetails($exercicio, $elementoId)
    {
        $dados = $this->repository->getArrecadadaPorElementoDetalhes($this->idCliente, $exercicio, $elementoId);

        $elemento = DB::table('ctbelemento')
            ->select('id', 'estrutural', 'nome as descricao')
            ->where('id', $elementoId)
            ->where('exercicio', $exercicio)
            ->where('idcliente', $this->idCliente)
            ->first();

        return view('receita.arrecadacao.elemento.details', compact('dados', 'exercicio', 'elemento'));
    }

    // --- EXECUÇÃO RECURSO (Exemplo 2 níveis) ---
    public function execucaoRecursoIndex()
    {
        $resumoAnual = $this->repository->getResumoGeralPorExercicio($this->idCliente);
        return view('receita.execucao.recurso.index', compact('resumoAnual'));
    }

    public function execucaoRecursoList($exercicio)
    {
        $dados = $this->repository->getExecutadaPorRecurso($this->idCliente, $exercicio);
        return view('receita.execucao.recurso.list', compact('dados', 'exercicio'));
    }

    // --- EXECUÇÃO POR ELEMENTO (2 Níveis) ---
    public function execucaoElementoIndex()
    {
        // Usa o resumo geral para listar os anos
        $resumoAnual = $this->repository->getResumoGeralPorExercicio($this->idCliente);
        return view('receita.execucao.elemento.index', compact('resumoAnual'));
    }

    public function execucaoElementoList($exercicio)
    {
        $dados = $this->repository->getExecutadaPorElemento($this->idCliente, $exercicio);

        return view('receita.execucao.elemento.list', compact('dados', 'exercicio'));
    }
}
