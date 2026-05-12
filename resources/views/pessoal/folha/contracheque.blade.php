@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <x-breadcrumb :items="$breadcrumb" />
        
        <div class="card shadow border-0 overflow-hidden">
            {{-- Cabeçalho do Card --}}
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center p-3">
                <h5 class="mb-0"><i class="fa fa-file-invoice-dollar me-2"></i>Demonstrativo de Pagamento</h5>
                <div class="d-print-none">
                    <button class="btn btn-sm btn-light" onclick="window.print()"><i class="fa fa-print me-1"></i>
                        Imprimir</button>
                </div>
            </div>

            <div class="card-body p-4">
                {{-- CABEÇALHO DO SERVIDOR --}}
                <div class="row mb-4 border-bottom pb-3">
                    <div class="col-md-6">
                        <small class="text-muted d-block text-uppercase">Servidor</small>
                        <span class="h5 fw-bold">{{ $contrato->nome }}</span>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted d-block text-uppercase">Matrícula</small>
                        <span class="fw-bold">{{ $contrato->matricula }}</span>
                    </div>
                    <div class="col-md-3 text-md-end">
                        <small class="text-muted d-block text-uppercase">Referência</small>
                        <span
                            class="badge bg-primary fs-6">{{ str_pad($mes, 2, '0', STR_PAD_LEFT) }}/{{ $exercicio }}</span>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-4">
                        <small class="text-muted d-block text-uppercase">Cargo / Função</small>
                        <span>{{ $contrato->funcao ?? 'NÃO INFORMADO' }}</span>
                    </div>
                    <div class="col-md-5">
                        <small class="text-muted d-block text-uppercase">Lotação</small>
                        <span>{{ $contrato->lotacao ?? 'NÃO INFORMADO' }}</span>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted d-block text-uppercase">Data Admissão</small>
                        <span>{{ date('d/m/Y', strtotime($contrato->admissao)) }}</span>
                    </div>
                </div>

                {{-- TABELA DE EVENTOS --}}
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="bg-light">
                            <tr class="text-muted small">
                                <th style="width: 80px">CÓD</th>
                                <th>DESCRIÇÃO DO EVENTO</th>
                                <th class="text-center" style="width: 100px">REF.</th>
                                <th class="text-end" style="width: 150px">VENCIMENTOS</th>
                                <th class="text-end" style="width: 150px">DESCONTOS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totP = 0;
                                $totD = 0;
                            @endphp
                            @foreach ($itens as $item)
                                <tr>
                                    <td class="text-center font-monospace">{{ $item->codigo }}</td>
                                    <td>
                                        {{ $item->descricao }}
                                        @if ($item->confidencial)
                                            <i class="fa fa-lock ms-1 text-muted small" title="Dado protegido"></i>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ number_format($item->referencia, 2, ',', '.') }}</td>
                                    <td class="text-end">
                                        {{ $item->tipo == 'P' ? number_format($item->valor, 2, ',', '.') : '-' }}
                                    </td>
                                    <td class="text-end text-danger">
                                        {{ $item->tipo == 'D' ? number_format($item->valor, 2, ',', '.') : '-' }}
                                    </td>
                                </tr>
                                @php
                                    if ($item->tipo == 'P') {
                                        $totP += $item->valor;
                                    }
                                    if ($item->tipo == 'D') {
                                        $totD += $item->valor;
                                    }
                                @endphp
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr class="fw-bold text-nowrap">
                                <td colspan="3" class="text-end">TOTAIS</td>
                                <td class="text-end">R$ {{ number_format($totP, 2, ',', '.') }}</td>
                                <td class="text-end text-danger">R$ {{ number_format($totD, 2, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                {{-- RESUMO LÍQUIDO --}}
                <div class="row mt-4">
                    <div class="col-md-8">
                        <div class="p-3 bg-light rounded border text-muted small">
                            <strong>Observação:</strong> Este documento é uma representação digital da folha de pagamento e
                            possui fins informativos conforme a Lei de Transparência.
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-primary text-white border-0 shadow-sm">
                            <div class="card-body text-center">
                                <small class="text-uppercase opacity-75">Valor Líquido</small>
                                <h3 class="mb-0 fw-bold">R$ {{ number_format($totP - $totD, 2, ',', '.') }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('layouts.partials.back')
    </div>

    <style>
        @media print {
            body {
                background: white;
            }

            .container {
                width: 100%;
                max-width: 100%;
            }

            .card {
                border: 1px solid #dee2e6 !important;
                shadow: none !important;
            }

            .d-print-none {
                display: none !important;
            }
        }
    </style>
@endsection
