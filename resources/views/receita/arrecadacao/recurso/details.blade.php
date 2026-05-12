@extends('layouts.app')

@section('content')
    {{-- Breadcrumb seguindo o padrão que você utiliza --}}
    <x-breadcrumb :items="[
        'Receita' => '#',
        'Arrecadação' => route('receita.arrecadacao.recurso.index'),
        'Por Recurso' => route('receita.arrecadacao.recurso.list', $exercicio),
        'Detalhamento do Recurso' => '',
    ]" />

    @include('layouts.partials.recurso')

    @php
        $columns = [
            ['label' => 'Mês de Referência', 'icone' => 'fa fa-calendar-alt', 'align' => 'text-start'],
            ['label' => 'Arrecadação Bruta', 'icone' => 'fa fa-arrow-up', 'align' => 'text-end'],
            ['label' => 'Deduções/Renúncias', 'icone' => 'fa fa-arrow-down', 'align' => 'text-end'],
            ['label' => 'Arrecadação Líquida', 'icone' => 'fa fa-wallet', 'align' => 'text-end'],
        ];
    @endphp

    <x-tabela-transparencia titulo="Fluxo Mensal de Receita" cor="primary" :colunas="$columns">
        @forelse($dados as $detalhe)
            <tr>
                {{-- Nome do Mês via Carbon --}}
                <td class="fw-bold text-uppercase text-secondary">
                    {{ \Carbon\Carbon::createFromFormat('m', $detalhe->mes)->translatedFormat('F') }}
                </td>

                <td class="text-end">
                    R$ {{ number_format($detalhe->valor_arrecadado ?? 0, 2, ',', '.') }}
                </td>

                <td class="text-end text-danger">
                    - R$ {{ number_format($detalhe->valor_deduzido ?? 0, 2, ',', '.') }}
                </td>

                <td class="text-end fw-bold text-primary">
                    R$ {{ number_format($detalhe->saldo ?? 0, 2, ',', '.') }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="text-center py-5 text-muted">
                    <i class="fa fa-exclamation-circle d-block mb-2 fa-2x"></i>
                    Não foram encontrados lançamentos mensais para este recurso no exercício de {{ $exercicio }}.
                </td>
            </tr>
        @endforelse

        {{-- Linha de Totais Acumulados --}}
        @if ($dados->count() > 0)
            <tfoot class="bg-light">
                <tr class="fw-bold text-nowrap">
                    <td class="text-uppercase">Total Acumulado no Ano</td>
                    <td class="text-end">R$ {{ number_format($dados->sum('valor_arrecadado'), 2, ',', '.') }}</td>
                    <td class="text-end text-danger">R$ {{ number_format($dados->sum('valor_deduzido'), 2, ',', '.') }}
                    </td>
                    <td class="text-end text-primary" style="font-size: 1.1rem;">
                        R$ {{ number_format($dados->sum('saldo'), 2, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        @endif
    </x-tabela-transparencia>
    @include('layouts.partials.back')
    </div>
@endsection
