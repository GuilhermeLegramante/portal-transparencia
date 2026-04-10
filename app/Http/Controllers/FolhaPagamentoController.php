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
        $this->idCliente = config('app.client_id');
    }

    public function porFuncao(Request $request)
    {
        $exercicio = $request->get('exercicio', date('Y'));
        $mes = $request->get('mes', date('n'));

        $resumo = $this->repo->getResumoFinanceiro($this->idCliente);
        $dados = $this->repo->getPorFuncao($this->idCliente, $exercicio, $mes);

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

        $resumo = $this->repo->getResumoFinanceiro($this->idCliente);
        $dados = $this->repo->getPorLotacao($this->idCliente, $exercicio, $mes);

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

        $resumo = $this->repo->getResumoFinanceiro($this->idCliente);
        $dados = $this->repo->getPorRegime($this->idCliente, $exercicio, $mes);

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

        $resumo = $this->repo->getResumoFinanceiro($this->idCliente);
        $dados = $this->repo->getPorServidor($this->idCliente, $exercicio, $mes, $busca);

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

        $contratos = $this->repo->getContratosPorCategoria($this->idCliente, $exercicio, $mes, $campoFiltro, $idCategoria);

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
        $itens = $this->repo->getItensContracheque($this->idCliente, $exercicio, $mes, $idContrato);
        $contrato = $this->repo->getDadosContratoContracheque($this->idCliente, $idContrato);

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
