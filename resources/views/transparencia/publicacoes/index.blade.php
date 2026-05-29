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
            ['label' => 'Categorias', 'align' => 'text-start'],
            ['label' => 'Documento', 'align' => 'text-center'],
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
                </tr>
            @endforeach

            <tfoot class="table-light fw-bold">
                <tr class="text-nowrap">
                    <td colspan="4" class="text-end text-secondary">Total de Registros Encontrados:</td>
                    <td class="text-center text-dark">{{ count($publicacoes) }}</td>
                </tr>
            </tfoot>
        </x-tabela-transparencia>
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
