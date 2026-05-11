@extends('layouts.app')

@section('content')
    <div class="container py-4">
        {{-- Breadcrumb alinhado ao contexto de Órgãos --}}
        <x-breadcrumb cor="primary" :items="[
            'Despesa' => '#',
            'Empenho Orçamentário' => route('empenho.orgao.index'), {{-- Rota principal de órgãos --}}
            'Por Órgão' => '',
            $exercicio => '',
        ]" />

        @php
            $columns = [
                ['label' => '', 'icone' => 'search', 'align' => 'text-center'],
                ['label' => 'Nome', 'icone' => '', 'align' => 'text-start'],
                ['label' => 'Empenhado', 'icone' => '', 'align' => 'text-end'],
                ['label' => 'Anulado', 'icone' => '', 'align' => 'text-end'],
                ['label' => 'Liquidado', 'icone' => '', 'align' => 'text-end'],
                ['label' => 'Pago', 'icone' => '', 'align' => 'text-end'],
                ['label' => 'Saldo empenhado', 'icone' => '', 'align' => 'text-end'],
                ['label' => 'Saldo liquidar', 'icone' => '', 'align' => 'text-end'],
                ['label' => 'Saldo pagar', 'icone' => '', 'align' => 'text-end'],
            ];
        @endphp

        {{-- Título e cor (roxo) conforme a imagem enviada --}}
        <x-tabela-transparencia titulo="Empenhado para o exercício {{ $exercicio }}" cor="primary" :colunas="$columns">
            @foreach ($orgaos as $row)
                <tr>
                    {{-- Ação: Detalhes do Órgão --}}
                    <td class="text-center align-middle">
                        <a href="{{ route('empenho.orgao.detalhes', ['exercicio' => $exercicio, 'orgao_id' => $row->orgao_id]) }}"
                            class="btn btn-sm btn-outline-secondary" title="Ver detalhes do órgão">
                            <i class="fa fa-search"></i>
                        </a>
                    </td>

                    {{-- Nome do Órgão --}}
                    <td class="text-start align-middle small text-uppercase">
                        {{ $row->orgao_nome }}
                    </td>

                    {{-- Valores --}}
                    <td class="text-end align-middle">R$ {{ number_format($row->total_empenhado, 2, ',', '.') }}</td>
                    <td class="text-end align-middle text-muted">R$ {{ number_format($row->total_anulado, 2, ',', '.') }}</td>
                    <td class="text-end align-middle">R$ {{ number_format($row->total_liquidado, 2, ',', '.') }}</td>
                    <td class="text-end align-middle">R$ {{ number_format($row->total_pago, 2, ',', '.') }}</td>

                    {{-- Saldos --}}
                    <td class="text-end align-middle">R$ {{ number_format($row->saldo_empenhado, 2, ',', '.') }}</td>
                    <td class="text-end align-middle">R$ {{ number_format($row->saldo_liquidar, 2, ',', '.') }}</td>
                    <td class="text-end align-middle fw-bold text-primary">
                        {{ number_format($row->saldo_pagar, 2, ',', '.') }}
                    </td>
                </tr>
            @endforeach

            <tfoot class="table-light fw-bold">
                <tr class="text-nowrap">
                    <td colspan="2" class="text-end">TOTAIS DO EXERCÍCIO:</td>
                    <td class="text-end">R$ {{ number_format($orgaos->sum('total_empenhado'), 2, ',', '.') }}</td>
                    <td class="text-end text-muted">R$ {{ number_format($orgaos->sum('total_anulado'), 2, ',', '.') }}</td>
                    <td class="text-end">R$ {{ number_format($orgaos->sum('total_liquidado'), 2, ',', '.') }}</td>
                    <td class="text-end">R$ {{ number_format($orgaos->sum('total_pago'), 2, ',', '.') }}</td>
                    <td class="text-end">R$ {{ number_format($orgaos->sum('saldo_empenhado'), 2, ',', '.') }}</td>
                    <td class="text-end">R$ {{ number_format($orgaos->sum('saldo_liquidar'), 2, ',', '.') }}</td>
                    <td class="text-end text-primary">R$ {{ number_format($orgaos->sum('saldo_pagar'), 2, ',', '.') }}</td>
                </tr>
            </tfoot>
        </x-tabela-transparencia>
        
        @include('layouts.partials.back')
    </div>
@endsection