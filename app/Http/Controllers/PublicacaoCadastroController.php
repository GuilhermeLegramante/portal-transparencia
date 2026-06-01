<?php

namespace App\Http\Controllers;

use App\Http\Requests\PublicacaoStoreRequest;
use App\Repositories\PublicacaoCadastroRepository;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon; // <--- Importação importante para manipular a data
use Exception;

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
     * Remove a publicação do banco (independente do tipo) e o arquivo do disco
     */
    public function destroy($id)
    {
        try {
            $idcliente = config('app.client_id');

            // Chama o novo método do repositório que varre as duas tabelas
            $publicacaoDeletada = $this->repo->excluirPublicacaoDinamica($id, $idcliente);

            if ($publicacaoDeletada) {
                // Remove o arquivo físico do Storage se ele existir
                if (!empty($publicacaoDeletada->path)) {
                    if (Storage::disk('public')->exists($publicacaoDeletada->path)) {
                        Storage::disk('public')->delete($publicacaoDeletada->path);
                    }
                }

                return redirect()
                    ->back()
                    ->with('success', 'Registro e arquivo excluídos com sucesso!');
            }

            return redirect()->back()->with('error', 'Publicação ou Prestação de contas não encontrada.');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Erro ao tentar excluir: ' . $e->getMessage());
        }
    }
}
