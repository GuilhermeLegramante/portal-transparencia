@php
    $columns = [
        ['label' => 'Exercício', 'icone' => '', 'align' => 'text-center'],
        ['label' => 'Total', 'icone' => '', 'align' => 'text-end'],
        ['label' => 'Ação', 'icone' => '', 'align' => 'text-center'],
    ];
@endphp
<x-tabela-transparencia titulo="Resumo da movimentação por exercício" cor="primary" :colunas="$columns">
    @forelse($resumoAnual as $resumo)
        <tr>
            <td class="text-center fw-bold">{{ $resumo->exercicio }}</td>
            <td class="text-end">
                {{ number_format($resumo->valor, 2, ',', '.') }}
            </td>
            <td class="text-center">
                <a href="{{ route('despesa.diarias.detalhe', $resumo->exercicio) }}"
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
