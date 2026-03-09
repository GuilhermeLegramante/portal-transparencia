@extends('layouts.app')

@section('content')
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb bg-light p-2 rounded">
                <li class="breadcrumb-item"><a href="/" class="text-decoration-none text-muted">Planejamento</a></li>
                <li class="breadcrumb-item text-muted">LOA</li>
                <li class="breadcrumb-item text-muted">Despesa</li>
                <li class="breadcrumb-item active text-danger fw-bold" aria-current="page">Por elemento de despesa</li>
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
                ['label' => 'Exercício', 'icone' => 'calendar', 'align' => 'text-center'],
                ['label' => 'Total', 'icone' => 'dollar-sign', 'align' => 'text-end'],
                ['label' => 'Ação', 'icone' => 'external-link-alt', 'align' => 'text-center'],
            ];
        @endphp

        <x-tabela-transparencia titulo="Resumo da movimentação por exercício" cor="primary" :colunas="$columns">
            @forelse($resumoAnual as $resumo)
                <tr>
                    <td class="text-center fw-bold">{{ $resumo->exercicio }}</td>
                    <td class="text-end">
                        {{ number_format($resumo->valor_orcado, 2, ',', '.') }}
                    </td>
                    <td class="text-center">
                        <a href="{{ route('planejamento.loa.despesa.elemento.detalhe', $resumo->exercicio) }}"
                            class="btn btn-outline-secondary btn-sm">
                            <i class="fa fa-edit me-1"></i> Visualizar
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center py-3">Nenhum resumo encontrado.</td>
                </tr>
            @endforelse
        </x-tabela-transparencia>

    </div>
@endsection
