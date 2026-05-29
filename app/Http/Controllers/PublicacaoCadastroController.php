<?php

namespace App\Http\Controllers;

use App\Http\Requests\PublicacaoStoreRequest;
use App\Repositories\PublicacaoCadastroRepository;
use Illuminate\Support\Facades\Storage;
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
            // Salva em: storage/app/public/cliente_{id}/publicacoes/nome_hash.pdf
            $subPasta = "cliente_{$idcliente}/publicacoes";
            $pathSalvo = $arquivo->store($subPasta, 'public');

            if (!$pathSalvo) {
                throw new Exception("Falha ao salvar arquivo físico no disco.");
            }

            // Prepara a estrutura base de dados
            $dadosBase = [
                'idcliente' => $idcliente,
                'exercicio' => $request->input('exercicio'),
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
}
