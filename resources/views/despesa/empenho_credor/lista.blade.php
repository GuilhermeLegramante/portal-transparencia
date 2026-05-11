@extends('layouts.app')
@section('content')
    <div class="container py-4">
        <x-breadcrumb cor="primary" :items="[
            'Despesa' => '#',
            'Empenho Orçamentário' => route('empenho.credor.index'),
            'Credores de ' . $exercicio => '',
        ]" />


        @php
            $columns = [
                ['label' => '', 'icone' => 'fa-search', 'align' => 'text-center'], // Coluna da Lupa
                ['label' => 'Nome do Credor', 'icone' => '', 'align' => 'text-start'],
                ['label' => 'Empenhado', 'icone' => '', 'align' => 'text-end'],
                ['label' => 'Liquidado', 'icone' => '', 'align' => 'text-end'],
                ['label' => 'Saldo a Pagar', 'icone' => '', 'align' => 'text-end'],
            ];
        @endphp

        <x-tabela-transparencia titulo="Credores com empenhos em {{ $exercicio }}" cor="primary" :colunas="$columns">
            @foreach ($credores as $credor)
                <tr>
                    <td class="text-center align-middle">
                        <a href="{{ route('empenho.credor.detalhes', [$exercicio, $credor->credor_id]) }}"
                            class="btn btn-sm btn-outline-secondary" title="Ver detalhes de {{ $credor->nome }}">
                            <i class="fa fa-search"></i>
                        </a>
                    </td>
                    <td class="text-start align-middle">{{ $credor->nome }}</td>
                    <td class="text-end align-middle">R$ {{ number_format($credor->total_empenhado, 2, ',', '.') }}</td>
                    <td class="text-end align-middle">R$ {{ number_format($credor->total_liquidado, 2, ',', '.') }}</td>
                    <td class="text-end fw-bold text-danger align-middle">
                        R$ {{ number_format($credor->saldo_pagar, 2, ',', '.') }}</td>

                </tr>
            @endforeach

            <tfoot class="table-light fw-bold">
                <tr class="text-nowrap">
                    <td colspan="2" class="text-end">TOTAIS:</td>
                    <td class="text-end">
                        R$ {{ number_format($credores->sum('total_empenhado'), 2, ',', '.') }}
                    </td>
                    <td class="text-end">
                        R$ {{ number_format($credores->sum('total_liquidado'), 2, ',', '.') }}
                    </td>
                    <td class="text-end text-danger">
                        R$ {{ number_format($credores->sum('saldo_pagar'), 2, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        </x-tabela-transparencia>
        @include('layouts.partials.back')
    </div>
@endsection
