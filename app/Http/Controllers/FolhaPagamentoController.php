<?php

namespace App\Http\Controllers;

use App\Repositories\FolhaPagamentoRepository;
use Illuminate\Http\Request;

class FolhaPagamentoController extends Controller
{
    protected $repo;
    private $idCliente;

    public function __construct(FolhaPagamentoRepository $repo)
    {
        $this->repo = $repo;
        
    }

    public function porFuncao(Request $request)
    {
        $exercicio = $request->get('exercicio', date('Y'));
        $mes = $request->get('mes', date('n'));

        $resumo = $this->repo->getResumoFinanceiro(config('app.client_id'));
        $dados = $this->repo->getPorFuncao(config('app.client_id'), $exercicio, $mes);

        $breadcrumb = [
            'Pessoal' => '#',
            'Folha de Pagamento' => '#',
            'Por Função' => ''
        ];

        return view('pessoal.folha.index_agrupado', compact('resumo', 'dados', 'exercicio', 'mes', 'breadcrumb'))
            ->with(['titulo' => 'Por Função', 'tipo' => 'funcao']);
    }

    public function porLotacao(Request $request)
    {
        $exercicio = $request->get('exercicio', date('Y'));
        $mes = $request->get('mes', date('n'));

        $resumo = $this->repo->getResumoFinanceiro(config('app.client_id'));
        $dados = $this->repo->getPorLotacao(config('app.client_id'), $exercicio, $mes);

        $breadcrumb = [
            'Pessoal' => '#',
            'Folha de Pagamento' => '#',
            'Por Lotação' => ''
        ];

        return view('pessoal.folha.index_agrupado', compact('resumo', 'dados', 'exercicio', 'mes', 'breadcrumb'))
            ->with(['titulo' => 'Por Lotação', 'tipo' => 'lotacao']);
    }

    public function porRegime(Request $request)
    {
        $exercicio = $request->get('exercicio', date('Y'));
        $mes = $request->get('mes', date('n'));

        $resumo = $this->repo->getResumoFinanceiro(config('app.client_id'));
        $dados = $this->repo->getPorRegime(config('app.client_id'), $exercicio, $mes);

        $breadcrumb = [
            'Pessoal' => '#',
            'Folha de Pagamento' => '#',
            'Por Regime' => ''
        ];

        return view('pessoal.folha.index_agrupado', compact('resumo', 'dados', 'exercicio', 'mes', 'breadcrumb'))
            ->with(['titulo' => 'Por Regime', 'tipo' => 'regime']);
    }

    public function porServidor(Request $request)
    {
        $exercicio = $request->get('exercicio', date('Y'));
        $mes = $request->get('mes', date('n'));
        $busca = $request->get('busca');

        $resumo = $this->repo->getResumoFinanceiro(config('app.client_id'));
        $dados = $this->repo->getPorServidor(config('app.client_id'), $exercicio, $mes, $busca);

        $breadcrumb = [
            'Pessoal' => '#',
            'Folha de Pagamento' => '#',
            'Por Servidor' => ''
        ];

        return view('pessoal.folha.index_servidor', compact('resumo', 'dados', 'exercicio', 'mes', 'busca', 'breadcrumb'));
    }

    public function showContratos($tipo, $exercicio, $mes, $idCategoria)
    {
        $mapaCampos = ['funcao' => 'idfuncao', 'lotacao' => 'idlotacao', 'regime' => 'idregime'];
        $campoFiltro = $mapaCampos[$tipo] ?? 'idfuncao';

        $contratos = $this->repo->getContratosPorCategoria(config('app.client_id'), $exercicio, $mes, $campoFiltro, $idCategoria);

        // Breadcrumb dinâmico baseado na origem
        $breadcrumb = [
            'Pessoal' => '#',
            'Folha de Pagamento' => route("pessoal.folha.$tipo"),
            'Servidores do Grupo' => ''
        ];

        return view('pessoal.folha.detalhe_contratos', compact('contratos', 'exercicio', 'mes', 'tipo', 'breadcrumb'));
    }

    public function showContracheque($exercicio, $mes, $idContrato)
    {
        $itens = $this->repo->getItensContracheque(config('app.client_id'), $exercicio, $mes, $idContrato);
        $contrato = $this->repo->getDadosContratoContracheque(config('app.client_id'), $idContrato);

        if (!$contrato) {
            return redirect()->back()->with('error', 'Contrato não encontrado.');
        }

        // Determina de onde o usuário possivelmente veio para montar o link de volta
        $origem = request('tipo', 'servidor');

        $breadcrumb = [
            'Pessoal' => '#',
            'Folha de Pagamento' => route("pessoal.folha.$origem"),
            'Contracheque' => ''
        ];

        return view('pessoal.folha.contracheque', compact('itens', 'contrato', 'exercicio', 'mes', 'breadcrumb'));
    }
}
