@extends('layouts.app')

@section('content')
    <x-breadcrumb :items="[
        'Receita' => '#',
        'Execução Orçamentária' => route('receita.execucao.elemento.index'),
        'Por Elemento' => '',
        'Exercício ' . $exercicio => '',
    ]" />

    <div class="container-fluid">
        @php
            $columns = [
                ['label' => 'Estrutural', 'icone' => 'fa fa-list-ol', 'align' => 'text-center'],
                ['label' => 'Descrição', 'icone' => 'fa fa-info-circle', 'align' => 'text-start'],
                ['label' => 'Vlr. Orçado', 'icone' => 'fa fa-coins', 'align' => 'text-end'],
                ['label' => 'Vlr. Executado', 'icone' => 'fa fa-check-double', 'align' => 'text-end'],
                ['label' => 'Diferença', 'icone' => 'fa fa-balance-scale', 'align' => 'text-end'],
            ];
        @endphp

        <x-tabela-transparencia titulo="Execução por Elemento de Receita - {{ $exercicio }}" cor="primary"
            :colunas="$columns">
            @foreach ($dados as $item)
                @php
                    $dif = ($item->valor_executado ?? 0) - ($item->valor_orcado ?? 0);
                @endphp
                <tr>
                    <td class="text-center text-muted small">{{ $item->estrutural }}</td>
                    <td class="fw-bold">{{ $item->descricao }}</td>
                    <td class="text-end">R$ {{ number_format($item->valor_orcado ?? 0, 2, ',', '.') }}</td>
                    <td class="text-end text-primary fw-bold">R$
                        {{ number_format($item->valor_executado ?? 0, 2, ',', '.') }}</td>
                    <td class="text-end {{ $dif >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ $dif >= 0 ? '+' : '' }} {{ number_format($dif, 2, ',', '.') }}
                    </td>
                </tr>
            @endforeach
        </x-tabela-transparencia>

        {{-- RODAPÉ COM TOTAIS --}}
        <tfoot class="table-light fw-bold">
            <tr>
                <td></td> {{-- Coluna Estrutural --}}
                <td class="text-end">TOTAIS:</td> {{-- Coluna Descrição --}}

                <td class="text-end">
                    R$ {{ number_format($dados->sum('valor_orcado'), 2, ',', '.') }}
                </td>

                <td class="text-end text-primary">
                    R$ {{ number_format($dados->sum('valor_executado'), 2, ',', '.') }}
                </td>

                @php
                    $totalDif = $dados->sum('valor_executado') - $dados->sum('valor_orcado');
                @endphp
                <td class="text-end {{ $totalDif >= 0 ? 'text-success' : 'text-danger' }}">
                    {{ $totalDif >= 0 ? '+' : '' }} {{ number_format($totalDif, 2, ',', '.') }}
                </td>
            </tr>
        </tfoot>
        @include('layouts.partials.back')
    </div>
@endsection
