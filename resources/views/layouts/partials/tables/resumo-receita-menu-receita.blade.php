@php
    $columns = [
        ['label' => 'Exercício', 'icone' => 'fa fa-calendar', 'align' => 'text-center'],
        ['label' => 'Vlr. Orçado', 'icone' => 'fa fa-coins', 'align' => 'text-end'],
        ['label' => 'Vlr. Realizado', 'icone' => 'fa fa-check-circle', 'align' => 'text-end'],
        ['label' => '% Realizado', 'icone' => 'fa fa-chart-line', 'align' => 'text-center'],
        ['label' => 'Ação', 'icone' => '', 'align' => 'text-center'],
    ];
@endphp

<x-tabela-transparencia titulo="Resumo por exercício" cor="primary" :colunas="$columns">
    @forelse($resumoAnual as $resumo)
        <tr>
            <td class="text-center fw-bold">{{ $resumo->exercicio }}</td>
            <td class="text-end">R$ {{ number_format($resumo->valor_orcado, 2, ',', '.') }}</td>
            <td class="text-end fw-bold text-success">R$ {{ number_format($resumo->valor_executado, 2, ',', '.') }}</td>
            <td class="text-center">
                <x-percentual-progress :valor="$resumo->percentual_realizado" />
            </td>
            <td class="text-center">
                <a href="{{ route($detailsRoute, $resumo->exercicio) }}" class="btn btn-action-view btn-sm shadow-sm">
                    <i class="fa fa-eye me-1"></i> Detalhes
                </a>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="5" class="text-center py-4">Nenhum registro encontrado.</td>
        </tr>
    @endforelse
</x-tabela-transparencia>
@include('layouts.partials.back')
