@extends('layouts.app')

@section('content')
    {{-- Breadcrumb ajustado para o contexto de Elementos --}}
    <x-breadcrumb cor="primary" :items="[
        'Despesa' => '#',
        'Empenho Orçamentário' => route('empenho.credor.index'),
        'Resumo por Elemento em ' . $exercicio => '',
    ]" />

    @php
        $columns = [
            ['label' => '', 'icone' => 'search', 'align' => 'text-center'],
            ['label' => 'Nome do Elemento', 'icone' => 'tag', 'align' => 'text-start'],
            ['label' => 'Empenhado', 'icone' => '', 'align' => 'text-end'],
            ['label' => 'Anulado', 'icone' => '', 'align' => 'text-end'],
            ['label' => 'Liquidado', 'icone' => '', 'align' => 'text-end'],
            ['label' => 'Pago', 'icone' => '', 'align' => 'text-end'],
            ['label' => 'Saldo Empenhado', 'icone' => '', 'align' => 'text-end'],
            ['label' => 'Saldo a Pagar', 'icone' => '', 'align' => 'text-end'],
        ];
    @endphp

    <x-tabela-transparencia titulo="Resumo por Elemento de Despesa - {{ $exercicio }}" cor="primary" :colunas="$columns">
        @foreach ($elementos as $elemento)
            <tr>
                {{-- Coluna de Ação (Lupa) --}}
                <td class="text-center align-middle">
                    {{-- Aqui você deve ajustar a rota para o detalhamento do elemento --}}
                    <a href="{{ route('empenho.elemento.detalhes', ['exercicio' => $exercicio, 'elemento_id' => $elemento->elemento_id]) }}"
                        class="btn btn-sm btn-outline-secondary" title="Ver detalhes">
                        <i class="fa fa-search"></i>
                    </a>
                </td>

                {{-- Nome / Descrição do Elemento --}}
                <td class="text-start align-middle small ">
                    {{ $elemento->estrutural }} - {{ $elemento->nome }}
                </td>

                {{-- Valores Principais --}}
                <td class="text-end align-middle">R$ {{ number_format($elemento->total_empenhado, 2, ',', '.') }}</td>
                <td class="text-end align-middle text-muted">R$ {{ number_format($elemento->total_anulado, 2, ',', '.') }}
                </td>
                <td class="text-end align-middle">R$ {{ number_format($elemento->total_liquidado, 2, ',', '.') }}</td>
                <td class="text-end align-middle">R$ {{ number_format($elemento->total_pago, 2, ',', '.') }}</td>

                {{-- Saldos de Destaque --}}
                <td class="text-end align-middle fw-bold">
                    R$ {{ number_format($elemento->saldo_empenhado, 2, ',', '.') }}
                </td>

                <td class="text-end align-middle fw-bold text-primary">
                    R$ {{ number_format($elemento->saldo_pagar, 2, ',', '.') }}
                </td>
            </tr>
        @endforeach

        {{-- Rodapé opcional para somar os totais da página --}}
        <tfoot class="table-light fw-bold">
            <tr>
                <td colspan="2" class="text-end">TOTAIS:</td>
                <td class="text-end">R$ {{ number_format($elementos->sum('total_empenhado'), 2, ',', '.') }}</td>
                <td class="text-end">R$ {{ number_format($elementos->sum('total_anulado'), 2, ',', '.') }}</td>
                <td class="text-end">R$ {{ number_format($elementos->sum('total_liquidado'), 2, ',', '.') }}</td>
                <td class="text-end">R$ {{ number_format($elementos->sum('total_pago'), 2, ',', '.') }}</td>
                <td class="text-end">R$ {{ number_format($elementos->sum('saldo_empenhado'), 2, ',', '.') }}</td>
                <td class="text-end text-primary">R$ {{ number_format($elementos->sum('saldo_pagar'), 2, ',', '.') }}
                </td>
            </tr>
        </tfoot>
    </x-tabela-transparencia>
    @include('layouts.partials.back')
@endsection
