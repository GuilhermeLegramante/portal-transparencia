@extends('layouts.app')

@section('content')
    {{-- Itens do Breadcrumb vindos do seu padrão --}}
    <x-breadcrumb :items="[
        'Receita' => '#',
        'Arrecadação' => route('receita.arrecadacao.elemento.index'),
        'Por Elemento' => route('receita.arrecadacao.elemento.list', $exercicio),
        'Detalhamento Mensal' => '',
    ]" />

    @include('layouts.partials.elemento-receita')

    <div class="container-fluid">
        @php
            $columns = [
                ['label' => 'Mês', 'icone' => 'fa fa-calendar-day', 'align' => 'text-start'],
                ['label' => 'Vlr. Arrecadado (Bruto)', 'icone' => 'fa fa-plus-circle', 'align' => 'text-end'],
                ['label' => 'Vlr. Deduzido', 'icone' => 'fa fa-minus-circle', 'align' => 'text-end'],
                ['label' => 'Saldo Líquido', 'icone' => 'fa fa-equals', 'align' => 'text-end'],
            ];
        @endphp

        <x-tabela-transparencia titulo="Evolução Mensal da Arrecadação - {{ $exercicio }}" cor="info"
            :colunas="$columns">
            @forelse($dados as $detalhe)
                <tr>
                    {{-- Converte o número do mês para nome por extenso --}}
                    <td class="fw-bold text-uppercase">
                        {{ \Carbon\Carbon::createFromFormat('m', $detalhe->mes)->translatedFormat('F') }}
                    </td>

                    <td class="text-end text-success">
                        R$ {{ number_format($detalhe->valor_arrecadado ?? 0, 2, ',', '.') }}
                    </td>

                    <td class="text-end text-danger">
                        - R$ {{ number_format($detalhe->valor_deduzido ?? 0, 2, ',', '.') }}
                    </td>

                    <td class="text-end fw-bold border-start">
                        R$ {{ number_format($detalhe->saldo ?? 0, 2, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center py-4 text-muted">Nenhum movimento registrado para este elemento.
                    </td>
                </tr>
            @endforelse

            {{-- Rodapé com Totais --}}
            @if ($dados->count() > 0)
                <tfoot class="table-light">
                    <tr class="fw-bold text-nowrap">
                        <td>TOTAL ACUMULADO</td>
                        <td class="text-end text-success">R$
                            {{ number_format($dados->sum('valor_arrecadado'), 2, ',', '.') }}</td>
                        <td class="text-end text-danger">R$ {{ number_format($dados->sum('valor_deduzido'), 2, ',', '.') }}
                        </td>
                        <td class="text-end bg-light">R$ {{ number_format($dados->sum('saldo'), 2, ',', '.') }}</td>
                    </tr>
                </tfoot>
            @endif
        </x-tabela-transparencia>
        @include('layouts.partials.back')
    </div>
@endsection
