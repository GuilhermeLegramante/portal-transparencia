@extends('layouts.app')

@section('content')
    <div class="container-fluid px-lg-5 py-4 bg-light-gray min-vh-100">
        <x-breadcrumb :items="$breadcrumb" />

        {{-- Bloco do Filtro --}}
        <div class="card shadow-sm border-0 rounded-3 mb-4 bg-white">
            <div class="card-body p-4">
                <form method="GET" action="{{ route('publicacoes.index') }}" class="row g-3 align-items-end">

                    {{-- Campo Exercício --}}
                    <div class="col-md-3">
                        <label class="small fw-bold text-muted text-uppercase mb-2 d-block">Exercício de Análise</label>
                        <input type="number" name="exercicio" class="form-control border-gray rounded-3"
                            value="{{ $exercicio }}" min="2000" max="{{ date('Y') + 1 }}">
                    </div>

                    {{-- NOVO: Dropdown de Categorias Dinâmico --}}
                    <div class="col-md-4">
                        <label class="small fw-bold text-muted text-uppercase mb-2 d-block">Filtrar por Categoria</label>
                        <select name="categoria" class="form-select border-gray rounded-3">
                            <option value="">Todas as categorias</option>
                            @foreach ($categoriasDisponiveis as $catDisponivel)
                                <option value="{{ $catDisponivel }}" {{ $categoria == $catDisponivel ? 'selected' : '' }}>
                                    {{ $catDisponivel }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Ações/Botões --}}
                    <div class="col-md-5 d-flex gap-2">
                        <button type="submit"
                            class="btn btn-primary rounded-3 px-4 fw-bold shadow-sm flex-grow-1 flex-md-grow-0">
                            <i class="fa fa-search me-2"></i>Filtrar
                        </button>

                        @if (!empty($categoria))
                            <a href="{{ route('publicacoes.index', ['exercicio' => $exercicio]) }}"
                                class="btn btn-outline-secondary rounded-3 px-3 d-inline-flex align-items-center">
                                <i class="fas fa-times me-2"></i> Limpar Filtro
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        {{-- Tabela de Dados --}}
        <x-tabela-transparencia titulo="Publicações Realizadas - Exercício {{ $exercicio }}" :colunas="[
            ['label' => 'Código', 'align' => 'text-center'],
            ['label' => 'Data', 'align' => 'text-center'],
            ['label' => 'Descrição', 'align' => 'text-start'],
            ['label' => 'Categoria', 'align' => 'text-start'],
            ['label' => 'Documento', 'align' => 'text-center'],
            ['label' => 'Ações', 'align' => 'text-center'],
        ]">
            @foreach ($publicacoes as $pub)
                <tr class="align-middle">
                    {{-- 1. Código --}}
                    <td class="text-center text-muted small fw-semibold">
                        #{{ $pub->codigo }}
                    </td>

                    {{-- 2. Data --}}
                    <td class="text-center text-secondary text-nowrap">
                        {{ date('d/m/Y', strtotime($pub->data)) }}
                    </td>

                    {{-- 3. Descrição --}}
                    <td class="text-start">
                        <span class="fw-bold text-dark d-block mb-1">{{ $pub->descricao }}</span>
                        <span class="text-muted small d-block d-md-none">Exercício: {{ $pub->exercicio }}</span>
                    </td>

                    {{-- 4. Categorias (Tags explodidas) --}}
                    <td>
                        @if ($pub->categoria)
                            <div class="d-flex flex-wrap gap-1">
                                @foreach (explode(';', $pub->categoria) as $tag)
                                    <span
                                        class="badge bg-light text-primary border border-primary-subtle px-2 py-1 rounded-pill small fw-semibold">
                                        {{ trim($tag) }}
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <span class="text-muted small italic">Geral</span>
                        @endif
                    </td>

                    {{-- 5. Link para o arquivo na Storage --}}
                    <td class="text-center">
                        @if ($pub->caminho_arquivo)
                            <a href="{{ asset('storage/' . $pub->caminho_arquivo) }}" target="_blank"
                                class="btn btn-white btn-sm shadow-sm border rounded-3 px-3 fw-bold text-nowrap text-secondary btn-documento"
                                title="Visualizar Documento">
                                <i class="fas fa-file-pdf text-danger me-2"></i> Visualizar
                            </a>
                        @else
                            <span class="text-muted small">—</span>
                        @endif
                    </td>

                    @auth
                        {{-- 6. NOVA COLUNA: Ações Administrativas (Excluir com Modal) --}}
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-outline-danger rounded-3 px-2 py-1"
                                data-bs-toggle="modal" data-bs-target="#modalConfirmarExclusao" data-id="{{ $pub->codigo }}"
                                data-descricao="{{ $pub->descricao }}" data-tipo="{{ $pub->tipo }}" {{-- <-- Agora lê direto: 'geral' ou 'prestacao' --}}
                                title="Excluir Publicação">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </td>
                    @else
                        {{-- Se o usuário não estiver autenticado, mostramos um placeholder ou nada --}}
                        <td class="text-center">
                            <span class="text-muted small fst-italic">Ações restritas a administradores</span>
                        </td>
                    @endauth

                </tr>
            @endforeach

            <tfoot class="table-light fw-bold">
                <tr class="text-nowrap">
                    <td colspan="4" class="text-end text-secondary">Total de Registros Encontrados:</td>
                    <td class="text-center text-dark">{{ count($publicacoes) }}</td>
                </tr>
            </tfoot>
        </x-tabela-transparencia>

        {{-- Estrutura da Modal de Confirmação Decente --}}
        <div class="modal fade" id="modalConfirmarExclusao" tabindex="-1" aria-labelledby="modalConfirmarExclusaoLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-3">

                    {{-- Cabeçalho Alerta --}}
                    <div class="modal-header bg-danger text-white border-0 py-3">
                        <h5 class="modal-title fw-bold d-flex align-items-center gap-2" id="modalConfirmarExclusaoLabel">
                            <i class="fas fa-exclamation-triangle"></i> Confirmar Exclusão
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    {{-- Corpo da Modal --}}
                    <div class="modal-body p-4 text-center">
                        <div class="mb-3 text-danger">
                            <i class="fas fa-trash-alt fa-3x animate__animated animate__shakeX"></i>
                        </div>
                        <p class="text-dark fw-semibold fs-5 mb-1">Você tem certeza absoluta?</p>
                        <p class="text-muted small mb-3">Esta ação é irreversível. O registro e o arquivo PDF serão
                            deletados permanentemente do servidor.</p>

                        {{-- Caixa destacando o item que será apagado --}}
                        <div class="bg-light rounded-3 p-3 text-start border">
                            <span class="d-block small fw-bold text-secondary text-uppercase mb-1">Item selecionado:</span>
                            <span id="modal-item-descricao" class="text-dark fw-bold text-break"></span>
                            <small class="d-block text-muted mt-1">Código do Registro: #<span
                                    id="modal-item-id"></span></small>
                        </div>
                    </div>

                    {{-- Rodapé com Ações --}}
                    <div class="modal-footer bg-light border-0 justify-content-center gap-2 py-3">
                        <button type="button" class="btn btn-outline-secondary rounded-3 px-4 fw-semibold"
                            data-bs-dismiss="modal">
                            Cancelar
                        </button>

                        {{-- O formulário real fica oculto aqui e será disparado pelo botão da modal --}}
                        <form id="form-excluir-publicacao" action="" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger rounded-3 px-4 fw-bold shadow-sm">
                                Sim, Excluir permanentemente
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>

        {{-- Script JavaScript para alimentar a modal dinamicamente --}}
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const modalExclusao = document.getElementById('modalConfirmarExclusao');

                if (modalExclusao) {
                    modalExclusao.addEventListener('show.bs.modal', function(event) {
                        // Botão da tabela que disparou o evento
                        const botao = event.relatedTarget;

                        // Extrai as informações dos atributos data-*
                        const idRegistro = botao.getAttribute('data-id');
                        const descricaoRegistro = botao.getAttribute('data-descricao');
                        const tipoRegistro = botao.getAttribute('data-tipo'); // <-- CORRIGIDO: Linha adicionada

                        // Monta a rota de exclusão do Laravel dinamicamente usando a rota nomeada
                        const urlBase = "{{ route('publicacoes.destroy', ':id') }}";
                        const urlFinal = urlBase.replace(':id', idRegistro);

                        // Atualiza os componentes internos da modal com os dados do item
                        document.getElementById('form-excluir-publicacao').setAttribute('action', urlFinal);
                        document.getElementById('modal-item-id').textContent = idRegistro;
                        document.getElementById('modal-item-descricao').textContent = descricaoRegistro;

                        // INJEÇÃO DO VALOR NO INPUT: Força o valor textualmente direto no atributo 'value'
                        const inputTipo = document.getElementById('modal-item-tipo');
                        if (inputTipo) {
                            inputTipo.value = tipoRegistro;
                        }
                    }); // <-- CORRIGIDO: Fechamento do Event Listener
                }
            });
        </script>
    </div>

    {{-- Estilos Auxiliares para o Design Clean --}}
    <style>
        .bg-light-gray {
            background-color: #f8f9fa;
        }

        .border-gray {
            border: 1px solid #dee2e6;
        }

        .btn-white {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            color: #475569;
            transition: all 0.2s ease;
        }

        .btn-white:hover {
            background-color: #f8fafc;
            border-color: #cbd5e1;
            color: #1e293b;
            transform: translateY(-1px);
        }

        .bg-light {
            background-color: #f1f5f9 !important;
        }

        .border-primary-subtle {
            border-color: #cbd5e1 !important;
        }
    </style>
@endsection
