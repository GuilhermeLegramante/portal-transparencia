@extends('layouts.app')

@section('content')
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb bg-light p-2 rounded">
                <li class="breadcrumb-item"><a href="/" class="text-decoration-none text-muted">Planejamento</a></li>
                <li class="breadcrumb-item text-muted">LOA</li>
                <li class="breadcrumb-item text-muted">Despesa</li>
                <li class="breadcrumb-item text-muted">
                    <a href="{{ route('planejamento.loa.despesa.elemento') }}">
                        Por elemento de despesa</a>
                </li>
                <li class="breadcrumb-item active text-danger fw-bold" aria-current="page">Exercício {{ $exercicio }}
                </li>
            </ol>
        </nav>

        <div class="card mb-3 border-0 shadow-sm">
            <div class="card-header bg-warning text-white d-flex justify-content-between align-items-center" role="button"
                data-bs-toggle="collapse" href="#collapseInfo" aria-expanded="true">
                <span><i class="fa fa-info-circle me-2"></i> Sobre a lei orçamentária anual</span>
                <i class="fa fa-chevron-down small"></i>
            </div>

            <div class="collapse show" id="collapseInfo">
                <div class="card-body text-secondary" style="font-size: 0.9rem;">
                    A Lei Orçamentária Anual (LOA) é uma lei elaborada pelo Poder Executivo que estabelece as despesas e as
                    receitas que serão realizadas no próximo ano. A Constituição determina que o Orçamento deve ser votado e
                    aprovado até o final de cada ano (também chamado sessão legislativa). A Lei Orçamentária Anual estima as
                    receitas e autoriza as despesas de acordo com a previsão de arrecadação.
                </div>
            </div>
        </div>

        @php
            $columns = [
                ['label' => 'Estrutural', 'icone' => 'sitemap', 'align' => 'text-center'],
                ['label' => 'Descrição', 'icone' => 'info-circle', 'align' => 'text-start'],
                ['label' => 'Valor Orçado', 'icone' => 'money-bill-wave', 'align' => 'text-end'],
                ['label' => 'Ações', 'icone' => 'eye', 'align' => 'text-center'],
            ];
        @endphp

        <x-tabela-transparencia titulo="Detalhamento por Elemento - Exercício {{ $exercicio }}" cor="primary"
            :colunas="$columns">
            @forelse($data as $item)
                <tr>
                    <td class="text-center text-muted small">{{ $item->estrutural }}</td>
                    <td class="text-start fw-bold" style="color: #4b647c;">{{ $item->descricao }}</td>
                    <td class="text-end fw-bold text-success">
                        R$ {{ number_format($item->valor_orcado, 2, ',', '.') }}
                    </td>
                    <td class="text-center">
                        <a href="" class="btn btn-sm btn-outline-primary">
                            <i class="fa fa-search"></i>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center py-4">Nenhum registro encontrado.</td>
                </tr>
            @endforelse
        </x-tabela-transparencia>

        <div class="d-flex justify-content-center mt-3">
            {{ $data->links('pagination::bootstrap-5') }}
        </div>

        <a href="{{ route('planejamento.loa.despesa.elemento') }}" class="btn btn-secondary">
            <i class="fa fa-arrow-left"></i> Voltar para o resumo por exercício
        </a>
    </div>

    @push('scripts')

    @endpush
@endsection
