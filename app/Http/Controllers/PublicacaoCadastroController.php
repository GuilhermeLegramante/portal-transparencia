<?php

namespace App\Http\Controllers;

use App\Http\Requests\PublicacaoStoreRequest;
use App\Repositories\PublicacaoCadastroRepository;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon; // <--- Importação importante para manipular a data
use Exception;
use Illuminate\Http\Request;

class PublicacaoCadastroController extends Controller
{
    protected $repo;

    public function __construct(PublicacaoCadastroRepository $repo)
    {
        $this->repo = $repo;
    }

    public function create()
    {
        $idcliente = config('app.client_id');
        $tags = $this->repo->getTagsGlobais();

        $breadcrumb = [
            'Transparência' => route('publicacoes.index'),
            'Publicações' => route('publicacoes.index'),
            'Novo Cadastro' => ''
        ];

        return view('transparencia.publicacoes.create', compact('tags', 'breadcrumb'));
    }

    public function store(PublicacaoStoreRequest $request)
    {
        try {
            $idcliente = config('app.client_id');

            // 1. Processa o Upload do Arquivo de forma segura organizada por cliente
            if (!$request->hasFile('arquivo') || !$request->file('arquivo')->isValid()) {
                return redirect()->back()->withInput()->with('error', 'Arquivo inválido para upload.');
            }

            $arquivo = $request->file('arquivo');
            $subPasta = "cliente_{$idcliente}/publicacoes";
            $pathSalvo = $arquivo->store($subPasta, 'public');

            if (!$pathSalvo) {
                throw new Exception("Falha ao salvar arquivo físico no disco.");
            }

            // --- RESOLUÇÃO DO ERRO DO CAMPO 'mes' ---
            // Extrai o mês da data enviada no input 'datahora' (ex: "2026-05-29T13:56" vira 5)
            $mes = null;
            if ($request->filled('datahora')) {
                $mes = Carbon::parse($request->input('datahora'))->month;
            }

            // Prepara a estrutura base de dados contendo o mês que o banco exige
            $dadosBase = [
                'idcliente' => $idcliente,
                'exercicio' => $request->input('exercicio'),
                'mes'       => $mes, // <--- Aqui está o campo que o banco pediu!
                'descricao' => $request->input('descricao'),
                'datahora'  => $request->input('datahora'),
                'path'      => $pathSalvo
            ];

            // 2. Roteia a gravação baseado no tipo selecionado
            if ($request->input('tipo_publicacao') === 'prestacao') {
                $dadosBase['categoria_texto'] = $request->input('categoria_texto');
                $this->repo->salvarPrestacaoContas($dadosBase);
            } else {
                $tagsSelecionadas = $request->input('tags', []);
                $this->repo->salvarPublicacaoGeral($dadosBase, $tagsSelecionadas);
            }

            return redirect()
                ->route('publicacoes.index', ['exercicio' => $dadosBase['exercicio']])
                ->with('success', 'Publicação cadastrada e documento armazenado com sucesso!');
        } catch (Exception $e) {
            // Se falhar e o arquivo já tiver sido salvo, deleta para não entupir o servidor de lixo
            if (isset($pathSalvo)) {
                Storage::disk('public')->delete($pathSalvo);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Erro ao salvar o registro: ' . $e->getMessage());
        }
    }

    /**
     * Remove o registro do banco de dados (da tabela correta) e o PDF físico do disco
     */
    public function destroy(Request $request, $id)
    {
        try {
            $idcliente = config('app.client_id');
            $tipo = $request->input('tipo_publicacao'); // Captura 'geral' ou 'prestacao' vindo da modal

            dd($tipo); // <--- Debug para verificar se o tipo está chegando corretamente

            if (empty($tipo)) {
                return redirect()->back()->with('error', 'O tipo de publicação não foi informado.');
            }

            // Executa a exclusão com base no ID e no Tipo exato
            $publicacaoDeletada = $this->repo->excluirPublicacaoPorTipo($id, $idcliente, $tipo);

            if ($publicacaoDeletada) {
                // Se o registro possuía um arquivo físico salvo, deleta-o do storage
                if (!empty($publicacaoDeletada->path)) {
                    if (Storage::disk('public')->exists($publicacaoDeletada->path)) {
                        Storage::disk('public')->delete($publicacaoDeletada->path);
                    }
                }

                return redirect()
                    ->back()
                    ->with('success', 'Registro e arquivo excluídos com sucesso!');
            }

            return redirect()->back()->with('error', 'O registro solicitado não foi encontrado.');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Erro ao tentar excluir o registro: ' . $e->getMessage());
        }
    }
}
