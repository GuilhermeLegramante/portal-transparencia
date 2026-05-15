<?php

namespace App\Http\Controllers;

use App\Repositories\EmpenhoRepository;
use App\Repositories\MunicipeRepository;
use Illuminate\Http\Request;

class EmpenhoOrgaoController extends Controller
{
    private $idCliente;

    protected $municipeRepo;
    protected $empenhoRepo;

    public function __construct(MunicipeRepository $municipeRepo, EmpenhoRepository $empenhoRepo)
    {
        $this->municipeRepo = $municipeRepo;
        $this->empenhoRepo = $empenhoRepo;

        
    }

    /**
     * Lista o resumo de empenhos agrupados por exercício
     */
    public function index()
    {
        $resumoAnual = $this->empenhoRepo->resumoAnualPorExercicio(config('app.client_id'));

        return view('despesa.empenho_orgao.index', compact('resumoAnual'));
    }

    /**
     * Lista os órgãos e seus respectivos totais dentro de um exercício específico
     */
    public function listaOrgaos($exercicio)
    {
        $orgaos = $this->empenhoRepo->listaOrgaos($exercicio, config('app.client_id'));

        return view('despesa.empenho_orgao.lista', compact('orgaos', 'exercicio'));
    }

    public function detalhes($exercicio, $orgao_id)
    {
        // 1. Busca os dados do Órgão para o Card de Identificação
        $orgao = $this->empenhoRepo->findOrgaoById((int)$orgao_id, (int)$exercicio, config('app.client_id'));

        if (!$orgao) {
            abort(404, 'Órgão não encontrado');
        }

        // 2. Busca a lista de empenhos vinculados a este órgão
        $empenhos = $this->empenhoRepo->findEmpenhosByOrgao((int)$orgao_id, (int)$exercicio, config('app.client_id'));

        // 3. Define as colunas (conforme a imagem que você enviou)
        $columns = [
            ['label' => '', 'icone' => 'search', 'align' => 'text-center'],
            ['label' => 'Número', 'icone' => '', 'align' => 'text-center'],
            ['label' => 'Emissão', 'icone' => '', 'align' => 'text-center'],
            ['label' => 'Saldo empenhado', 'icone' => '', 'align' => 'text-end'],
            ['label' => 'Saldo liquidar', 'icone' => '', 'align' => 'text-end'],
            ['label' => 'Saldo pagar', 'icone' => '', 'align' => 'text-end'],
        ];

        return view('despesa.empenho_orgao.detalhes', compact('orgao', 'empenhos', 'exercicio', 'columns'));
    }

    public function detalheEmpenho($exercicio, $orgao_id, $empenho_id)
    {
        $orgao = $this->empenhoRepo->findOrgaoById(
            (int)$orgao_id,
            (int)$exercicio,
            config('app.client_id')
        );

        $empenho = $this->empenhoRepo->findById($empenho_id, $exercicio, config('app.client_id'));

        $itens = $this->empenhoRepo->findItemsByEmpenhoId($empenho_id, config('app.client_id'));

        return view('despesa.empenho_orgao.detalhe-empenho', compact('orgao', 'empenho', 'itens', 'exercicio', 'orgao_id'));
    }
}
