@extends('layouts.app')

@section('content')
    <div class="container">
        {{-- Breadcrumb conforme o padrão --}}
        <x-breadcrumb :items="[
            'Despesa' => '/',
            'Diárias' => route('despesa.diarias.resumo'),
            'Exercício ' . $exercicio => route('despesa.diarias.detalhe', ['exercicio' => $exercicio]),
            $credor->nome => '',
        ]" />

        @include('layouts.partials.credor')

        @php
            $orientacaoPDF = 'landscape'; // Padrão para paisagem, pode ser alterado dinamicamente se necessário

            $columns = [
                ['label' => '', 'icone' => 'fa-search', 'align' => 'text-center'],
                ['label' => 'Número', 'icone' => '', 'align' => 'text-center'],
                ['label' => 'Emissão', 'icone' => '', 'align' => 'text-center'],
                ['label' => 'Tipo', 'icone' => '', 'align' => 'text-center'],
                ['label' => 'Saldo empenhado', 'icone' => '', 'align' => 'text-end'],
                ['label' => 'Saldo liquidar', 'icone' => '', 'align' => 'text-end'],
                ['label' => 'Saldo pagar', 'icone' => '', 'align' => 'text-end'],
            ];
        @endphp

        {{-- Componente Tabela (Bloco Verde conforme imagem) --}}
        <x-tabela-transparencia titulo="Empenhos emitidos para o exercício {{ $exercicio }}" cor="primary"
            :colunas="$columns">
            @foreach ($empenhos as $item)
                <tr>
                    <td class="text-center align-middle">
                        <a href="{{ route('despesa.diarias.empenho', [
                            'exercicio' => $exercicio,
                            'cad' => $credor->inscricao,
                            'emp' => $item->empenho_id,
                        ]) }}"
                            class="btn btn-sm btn-outline-secondary" title="Ver detalhes do empenho {{ $item->numero }}">
                            <i class="fa fa-search"></i>
                        </a>
                    </td>

                    <td class="text-center align-middle">{{ $item->numero }}</td>
                    <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->data_emissao)->format('d/m/Y') }}
                    </td>
                    <td class="text-center align-middle">
                        @if ($item->tipo == 'O')
                            <span
                                class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 rounded-pill px-3"
                                style="font-size: 0.75rem;">
                                ORÇAMENTÁRIO
                            </span>
                        @elseif($item->tipo == 'R')
                            <span
                                class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-3"
                                style="font-size: 0.75rem;">
                                RESTOS A PAGAR
                            </span>
                        @else
                            <span
                                class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 rounded-pill px-3"
                                style="font-size: 0.75rem;">
                                {{ $item->tipo }}
                            </span>
                        @endif
                    </td>
                    <td class="text-end align-middle">R$ {{ number_format($item->saldo_empenhado, 2, ',', '.') }}</td>
                    <td class="text-end align-middle">R$ {{ number_format($item->saldo_liquidar, 2, ',', '.') }}</td>
                    <td class="text-end fw-bold align-middle">R$ {{ number_format($item->saldo_pagar, 2, ',', '.') }}</td>
                </tr>
            @endforeach

            <tfoot class="table-light fw-bold border-top-2">
                <tr class="text-nowrap">
                    <td colspan="4" class="text-end">TOTAIS:</td>
                    <td class="text-end">R$ {{ number_format($empenhos->sum('saldo_empenhado'), 2, ',', '.') }}</td>
                    <td class="text-end">R$ {{ number_format($empenhos->sum('saldo_liquidar'), 2, ',', '.') }}</td>
                    <td class="text-end text-success">R$ {{ number_format($empenhos->sum('saldo_pagar'), 2, ',', '.') }}</td>
                </tr>
            </tfoot>
        </x-tabela-transparencia>

        @include('layouts.partials.back')
    </div>
@endsection
