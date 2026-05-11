@extends('layouts.app')
@section('content')
    <style>
        /* Força o comportamento correto da alternância sem quebrar o layout */
        .nav-tabs .nav-link.active {
            color: #0d6efd !important;
            /* Texto Azul quando ativa */
            background-color: #fff !important;
            /* Fundo Branco quando ativa */
        }

        .nav-tabs .nav-link:not(.active) {
            color: #ffffff !important;
            /* Texto Branco quando inativa */
            background-color: transparent !important;
            /* Fundo transparente quando inativa */
        }
    </style>
    <div class="container py-4">
        <x-breadcrumb :items="[
            'Despesa' => '#',
            'Empenho Orçamentário' => route('empenho.credor.index'),
            'Credores de ' . $exercicio => route('empenho.credor.lista', $exercicio),
            $credor->nome => '',
        ]" />

        @include('layouts.partials.credor')

        @php
            $columns = [
                ['label' => '', 'icone' => 'fa-search', 'align' => 'text-center'], // Coluna da Lupa
                ['label' => 'Número', 'icone' => '', 'align' => 'text-center'],
                ['label' => 'Emissão', 'icone' => '', 'align' => 'text-center'],
                ['label' => 'Tipo', 'icone' => '', 'align' => 'text-center'],
                ['label' => 'Saldo Empenhado', 'icone' => '', 'align' => 'text-end'],
                ['label' => 'Saldo a Pagar', 'icone' => '', 'align' => 'text-end'],
            ];
        @endphp

        <x-tabela-transparencia titulo="Empenhos emitidos em {{ $exercicio }}" cor="primary" :colunas="$columns">
            @foreach ($empenhos as $e)
                <tr>
                    {{-- Botão de Lupa --}}
                    <td class="text-center">
                        <a href="{{ route('empenho.credor.empenho.detalhe', ['exercicio' => $exercicio, 'credor_id' => $e->credor_id, 'empenho_id' => $e->id]) }}"
                            class="btn btn-sm btn-outline-secondary" title="Ver detalhes">
                            <i class="fa fa-search"></i>
                        </a>
                    </td>

                    <td class="text-center">{{ $e->numero }}</td>
                    <td class="text-center">{{ date('d/m/Y', strtotime($e->data_emissao)) }}</td>
                    <td class="text-center">
                        @if ($e->tipo == 'O')
                            <span
                                class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 rounded-pill px-3"
                                style="font-size: 0.75rem;">
                                ORÇAMENTÁRIO
                            </span>
                        @else
                            <span
                                class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-3"
                                style="font-size: 0.75rem;">
                                RESTOS A PAGAR
                            </span>
                        @endif
                    </td>
                    <td class="text-end">R$ {{ number_format($e->saldo_empenhado, 2, ',', '.') }}</td>
                    <td class="text-end fw-bold">R$ {{ number_format($e->saldo_pagar, 2, ',', '.') }}</td>
                </tr>
            @endforeach

            <tfoot class="table-light fw-bold">
                <tr class="text-nowrap">
                    <td colspan="4" class="text-end">TOTAIS:</td>
                    <td class="text-end">
                        R$ {{ number_format($empenhos->sum('saldo_empenhado'), 2, ',', '.') }}
                    </td>
                    <td class="text-end">
                        R$ {{ number_format($empenhos->sum('saldo_pagar'), 2, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        </x-tabela-transparencia>
        @include('layouts.partials.back')

    </div>

    <style>
        .nav-tabs .nav-link.active {
            background-color: #fff !important;
            color: var(--bs-primary) !important;
            border-bottom: none;
        }

        .nav-tabs .nav-link:hover {
            border-color: transparent;
            opacity: 0.8;
        }
    </style>
@endsection
