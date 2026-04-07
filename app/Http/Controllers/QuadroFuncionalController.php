<?php

namespace App\Http\Controllers;

use App\Repositories\QuadroFuncionalRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuadroFuncionalController extends Controller
{
    protected $repository;
    private $idCliente;

    public function __construct(QuadroFuncionalRepository $repository)
    {
        $this->repository = $repository;
        $this->idCliente = config('app.client_id');
    }

    // Auxiliar para carregar filtros e resumo
    private function getCommonData($request)
    {
        // Busca ID da situação "Em Atividade" (Código 1) como padrão
        $situacaoPadrao = DB::table('folsituacaocontratual')
            ->where('idcliente', $this->idCliente)
            ->where('codigo', 1)
            ->value('id');

        $sitId = $request->get('situacao', $situacaoPadrao);

        return [
            'resumo' => $this->repository->getResumoGeral($this->idCliente),
            'situacoes' => DB::table('folsituacaocontratual')->where('idcliente', $this->idCliente)->get(),
            'sitId' => $sitId,
            'idcliente' => $this->idCliente
        ];
    }

    public function porFuncao(Request $request)
    {
        $common = $this->getCommonData($request);
        $dados = $this->repository->getPorFuncao($common['idcliente'], $common['sitId']);

        return view('pessoal.quadro.funcao_list', array_merge($common, ['dados' => $dados]));
    }

    public function porLotacao(Request $request)
    {
        $common = $this->getCommonData($request);
        $dados = $this->repository->getPorLotacao($common['idcliente'], $common['sitId']);

        return view('pessoal.quadro.lotacao_list', array_merge($common, ['dados' => $dados]));
    }

    public function porRegime(Request $request)
    {
        $common = $this->getCommonData($request);
        $dados = $this->repository->getPorRegime($common['idcliente'], $common['sitId']);

        return view('pessoal.quadro.regime_list', array_merge($common, ['dados' => $dados]));
    }

    public function porServidor(Request $request)
    {
        $common = $this->getCommonData($request);
        $dados = $this->repository->getRelacaoNominal($common['idcliente'], $common['sitId']);

        return view('pessoal.quadro.servidor_list', array_merge($common, ['dados' => $dados]));
    }

    public function showDetalhes(Request $request, $tipo, $idCategoria)
    {
        $sitId = $request->get('situacao');

        // Define qual coluna filtrar no banco com base no tipo vindo da URL
        $mapa = [
            'funcao'  => ['coluna' => 'idfuncao', 'tabela' => 'folfuncao', 'titulo' => 'Função'],
            'lotacao' => ['coluna' => 'idlotacao', 'tabela' => 'follotacao', 'titulo' => 'Lotação'],
            'regime'  => ['coluna' => 'idregime', 'tabela' => 'folregime', 'titulo' => 'Regime'],
        ];

        $config = $mapa[$tipo];

        // Busca os servidores usando o método que já temos no Repository
        $servidores = $this->repository->getDetalhesContratos($this->idCliente, $sitId, $config['coluna'], $idCategoria);

        // Busca o nome da categoria para o cabeçalho (Ex: "Secretaria de Saúde" ou "Professor")
        $categoriaNome = DB::table($config['tabela'])
            ->where('id', $idCategoria)
            ->where('idcliente', $this->idCliente)
            ->value('nome');

        return view('pessoal.quadro.show_servidores', compact('servidores', 'categoriaNome', 'config'));
    }
}
