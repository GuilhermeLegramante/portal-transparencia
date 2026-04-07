@extends('layouts.app')

@section('content')
    <div class="container">
        <x-breadcrumb :items="[
            'Planejamento' => '/',
            'LOA' => '#',
            'Receita' => '#',
            'Por Elemento' => route('planejamento.loa.receita', ['filtro' => 'elemento']),
            'Exercício ' . $exercicio => '',
        ]" />

        @include('layouts.partials.cards.loa')

        @php
            $columns = [
                ['label' => 'Estrutural', 'icone' => '', 'align' => 'text-center'],
                ['label' => 'Descrição', 'icone' => '', 'align' => 'text-start'],
                ['label' => 'Percentual', 'icone' => '', 'align' => 'text-end'],
                ['label' => 'Valor Orçado', 'icone' => '', 'align' => 'text-end'],
            ];
        @endphp

        <x-tabela-transparencia titulo="Detalhamento por Elemento - Previsão para o Exercício {{ $exercicio }}"
            cor="primary" :colunas="$columns">
            @forelse($data as $item)
                <tr>
                    <td class="text-center text-muted small">{{ $item->estrutural }}</td>
                    <td class="text-start" style="color: #4b647c;">{{ $item->descricao }}</td>
                    <td class="text-end text-muted small">
                        {{ number_format(($item->valor_orcado / $totalGeralOrcado) * 100, 2, ',', '.') }}%</td>
                    <td class="text-end text-success">
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

        @include('layouts.partials.back')
    </div>

    @push('scripts')
    @endpush
@endsection
