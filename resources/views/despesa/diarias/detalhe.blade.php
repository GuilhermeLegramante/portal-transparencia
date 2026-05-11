@extends('layouts.app')

@section('content')
    <div class="container">
        <x-breadcrumb :items="[
            'Despesa' => '/',
            'Diárias' => '#',
            'Resumo por Exercício' => route('despesa.diarias.resumo'),
            'Detalhamento ' . $exercicio => '',
        ]" />
        
        @include('layouts.partials.cards.diarias')

        @php
            $orientacaoPDF = 'landscape'; // Padrão para paisagem, pode ser alterado dinamicamente se necessário

            $columns = [
                ['label' => '', 'icone' => 'fa-search', 'align' => 'text-center'], // Coluna da Lupa
                ['label' => 'Inscrição', 'icone' => 'fa-id-card', 'align' => 'text-start'], // Nova Coluna
                ['label' => 'Nome', 'icone' => 'fa-user', 'align' => 'text-start'],
                ['label' => 'Empenhado', 'icone' => '', 'align' => 'text-end'],
                ['label' => 'Anulado', 'icone' => '', 'align' => 'text-end'],
                ['label' => 'Liquidado', 'icone' => '', 'align' => 'text-end'],
                ['label' => 'Pago', 'icone' => '', 'align' => 'text-end'],
                ['label' => 'Saldo Empenhado', 'icone' => '', 'align' => 'text-end'],
                ['label' => 'Saldo a Liquidar', 'icone' => '', 'align' => 'text-end'],
                ['label' => 'Saldo a Pagar', 'icone' => '', 'align' => 'text-end'],
            ];
        @endphp

        <x-tabela-transparencia titulo="Empenhado para o exercício {{ $exercicio }}" cor="primary" :colunas="$columns">

            @forelse($data as $item)
                <tr>
                    {{-- Botão de Lupa --}}
                    <td class="text-center">
                        <a href="{{ route('despesa.diarias.credor', ['cad' => $item->idcredor, 'exercicio' => $exercicio]) }}"
                            class="btn btn-sm btn-outline-secondary" title="Ver detalhes de {{ $item->nome_municipe }}">
                            <i class="fa fa-search"></i>
                        </a>
                    </td>

                    {{-- Inscrição (idcredor) --}}
                    <td class="text-start align-middle small text-muted">
                        {{ $item->idcredor }}
                    </td>

                    <td class="text-start align-middle small" style="color: #4b647c;">
                        {{ $item->nome_municipe }}
                    </td>
                    <td class="text-end">R$ {{ number_format($item->empenhado, 2, ',', '.') }}</td>
                    <td class="text-end text-muted">R$ {{ number_format($item->anulado, 2, ',', '.') }}</td>
                    <td class="text-end">R$ {{ number_format($item->liquidado, 2, ',', '.') }}</td>
                    <td class="text-end">R$ {{ number_format($item->pago, 2, ',', '.') }}</td>
                    <td class="text-end fw-bold">R$ {{ number_format($item->saldo_empenhado, 2, ',', '.') }}</td>
                    <td class="text-end">R$ {{ number_format($item->saldo_liquidar, 2, ',', '.') }}</td>
                    <td class="text-end text-danger">R$ {{ number_format($item->saldo_pagar, 2, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center py-4">Nenhum registro encontrado para este exercício.</td>
                </tr>
            @endforelse

            {{-- Rodapé com Totais Gerais acumulados --}}
            <tfoot class="table-light fw-bold">
                <tr class="text-nowrap">
                    <td></td> {{-- Sob a Lupa --}}
                    <td></td> {{-- Sob a Inscrição --}}
                    <td class="text-end text-uppercase">Totais:</td>
                    <td class="text-end">R$ {{ number_format($totais->total_empenhado, 2, ',', '.') }}</td>
                    <td class="text-end text-muted">R$ {{ number_format($totais->total_anulado, 2, ',', '.') }}</td>
                    <td class="text-end">R$ {{ number_format($totais->total_liquidado, 2, ',', '.') }}</td>
                    <td class="text-end">R$ {{ number_format($totais->total_pago, 2, ',', '.') }}</td>
                    <td class="text-end text-primary">R$ {{ number_format($totais->total_saldo_empenhado, 2, ',', '.') }}</td>
                    <td class="text-end">R$ {{ number_format($totais->total_saldo_liquidar, 2, ',', '.') }}</td>
                    <td class="text-end text-danger">R$ {{ number_format($totais->total_saldo_pagar, 2, ',', '.') }}</td>
                </tr>
            </tfoot>
        </x-tabela-transparencia>

        @include('layouts.partials.back')
    </div>
@endsection
