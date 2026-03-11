@extends('layouts.app')

@section('content')
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb bg-light p-2 rounded">
                <li class="breadcrumb-item"><a href="/" class="text-decoration-none text-muted">Planejamento</a></li>
                <li class="breadcrumb-item text-muted">LOA</li>
                <li class="breadcrumb-item text-muted">Despesa</li>
                <li class="breadcrumb-item text-muted">
                    <a href="{{ route('planejamento.loa.despesa', ['filtro' => 'orgao']) }}">
                        Por Órgão</a>
                </li>
                <li class="breadcrumb-item active text-danger fw-bold" aria-current="page">Exercício {{ $exercicio }}
                </li>
            </ol>
        </nav>

        @include('layouts.partials.cards.loa')

        @php
            $columns = [
                ['label' => 'Código', 'icone' => '', 'align' => 'text-center'],
                ['label' => 'Nome', 'icone' => '', 'align' => 'text-start'],
                ['label' => 'Percentual', 'icone' => '', 'align' => 'text-end'],
                ['label' => 'Total', 'icone' => '', 'align' => 'text-end'],
            ];
        @endphp

        <x-tabela-transparencia titulo="Detalhamento por Órgão - Previsão para o Exercício {{ $exercicio }}"
            cor="primary" :colunas="$columns">
            @forelse($data as $item)
                <tr>
                    <td class="text-center text-muted small">{{ $item->codigo }}</td>
                    <td class="text-start" style="color: #4b647c;">{{ $item->descricao }}</td>
                    <td class="text-end text-muted small">
                        {{ number_format(($item->valor_orcado / $totalGeralOrcado) * 100, 2, ',', '.') }}%</td>
                    <td class="text-end  text-success">
                        R$ {{ number_format($item->valor_orcado, 2, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center py-4">Nenhum registro encontrado.</td>
                </tr>
            @endforelse

            {{-- Linha de Totalizador --}}
            <tfoot class="table-light">
                <tr>
                    <td colspan="3" class="text-end text-uppercase">Total do Exercício:</td>
                    <td class="text-end text-primary" style="font-size: 1.1rem;">
                        R$ {{ number_format($totalGeralOrcado, 2, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        </x-tabela-transparencia>

        <div class="d-flex justify-content-center mt-3">
            {{ $data->links('pagination::bootstrap-5') }}
        </div>

        <a href="{{ route('planejamento.loa.despesa', ['filtro' => 'orgao']) }}" class="btn btn-secondary">
            <i class="fa fa-arrow-left"></i> Voltar
        </a>
    </div>

    @push('scripts')
    @endpush
@endsection
