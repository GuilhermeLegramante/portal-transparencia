@extends('layouts.app')

@section('content')
    <div class="container-fluid px-lg-5 py-4 bg-light-gray min-vh-100">
        <x-breadcrumb :items="$breadcrumb" />

        {{-- Cabeçalho da Página com seletor de Exercício --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <div>
                <h1 class="h3 fw-bold text-dark mb-1">Indicadores Contábeis Estratégicos</h1>
                <p class="text-muted small mb-0">Acompanhamento executivo de despesas e limites consolidados do município.
                </p>
            </div>
            <div class="bg-white p-2 rounded-3 shadow-sm border" style="min-width: 190px;">
                <form method="GET" action="{{ route('gestor.indicadores.index') }}"
                    class="d-flex align-items-center justify-content-end gap-2 w-100">
                    <label class="small fw-bold text-secondary text-uppercase mb-0 text-nowrap px-1">Exercício:</label>
                    <input type="number" name="exercicio"
                        class="form-control border-0 bg-light fw-bold text-primary rounded-3 py-1 px-2 text-center"
                        value="{{ $exercicio }}" min="2000" max="{{ date('Y') + 1 }}" style="width: 90px;"
                        onchange="this.form.submit()">
                </form>
            </div>
        </div>

        @if (!$resumoAnual)
            <div class="alert alert-info rounded-3 border-0 shadow-sm p-4">
                <i class="fas fa-info-circle me-2"></i> Nenhum movimento contábil consolidado foi localizado para o
                exercício selecionado.
            </div>
        @else
            {{-- CARDS SUPERIORES: Principais Indicadores de Comprometimento --}}
            <div class="row g-4 mb-4">
                {{-- Card Exercício Atual --}}
                <div class="col-md-6 col-xl-3">
                    <div class="card h-100 border-0 shadow-sm rounded-3 bg-white border-start border-primary border-4">
                        <div class="card-body p-4">
                            <span class="text-muted text-uppercase small d-block fw-bold mb-1">Comprometido
                                ({{ $exercicio }})</span>
                            <h2 class="fw-bold text-dark mb-2">{{ number_format($pctComprometidoExercicio, 2, ',', '.') }}%
                            </h2>
                            <div class="progress rounded-pill mb-2" style="height: 6px;">
                                <div class="progress-bar bg-primary" role="progressbar"
                                    style="width: {{ $pctComprometidoExercicio }}%"></div>
                            </div>
                            <small class="text-muted">Despesa Empenhada / Total Atualizado</small>
                        </div>
                    </div>
                </div>

                {{-- Card Exercício Anterior --}}
                <div class="col-md-6 col-xl-3">
                    <div class="card h-100 border-0 shadow-sm rounded-3 bg-white border-start border-secondary border-4">
                        <div class="card-body p-4">
                            <span class="text-muted text-uppercase small d-block fw-bold mb-1">Comprometido
                                ({{ $exercicio - 1 }})</span>
                            <h2 class="fw-bold text-secondary mb-2">
                                {{ number_format($pctComprometidoAnterior, 2, ',', '.') }}%</h2>
                            <div class="progress rounded-pill mb-2" style="height: 6px;">
                                <div class="progress-bar bg-secondary" role="progressbar"
                                    style="width: {{ $pctComprometidoAnterior }}%"></div>
                            </div>
                            <small class="text-muted">Histórico do mesmo período anterior</small>
                        </div>
                    </div>
                </div>

                {{-- Total Atualizado Disponível --}}
                <div class="col-md-6 col-xl-3">
                    <div class="card h-100 border-0 shadow-sm rounded-3 bg-white">
                        <div class="card-body p-4">
                            <span class="text-muted text-uppercase small d-block fw-bold mb-1">Orçamento Atualizado</span>
                            <h3 class="fw-bold text-success mb-1">R$
                                {{ number_format($resumoAnual->valor_atualizado_exercicio, 2, ',', '.') }}</h3>
                            <small class="text-muted d-block mt-2">
                                <i class="fas fa-arrow-up text-success me-1"></i> Fixado + Remanejamentos
                            </small>
                        </div>
                    </div>
                </div>

                {{-- Total Pago --}}
                <div class="col-md-6 col-xl-3">
                    <div class="card h-100 border-0 shadow-sm rounded-3 bg-white">
                        <div class="card-body p-4">
                            <span class="text-muted text-uppercase small d-block fw-bold mb-1">Total Pago Líquido</span>
                            <h3 class="fw-bold text-info mb-1">R$
                                {{ number_format($resumoAnual->valor_pago_exercicio, 2, ',', '.') }}</h3>
                            <small class="text-muted d-block mt-2">
                                <i class="fas fa-check-double text-info me-1"></i> Desembolso financeiro real
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- BLOCO GRÁFICO: Evolução Mensal da Despesa Empenhada --}}
            <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
                <div class="card-body p-4">
                    <h5 class="fw-bold text-dark mb-3">Evolução de Empenhos Mensais (Histórico Comparativo)</h5>
                    <div style="position: relative; height:320px; width:100%">
                        <canvas id="chartEvolucaoDespesas"></canvas>
                    </div>
                </div>
            </div>

            {{-- BLOCO TABELA: Detalhamento Anualizado Geral --}}
            <x-tabela-transparencia titulo="Quadro Comparativo Consolidado das Despesas" :colunas="[
                ['label' => 'Métrica Contábil', 'align' => 'text-start'],
                ['label' => 'Exercício Anterior (' . ($exercicio - 1) . ')', 'align' => 'text-end'],
                ['label' => 'Exercício Atual (' . $exercicio . ')', 'align' => 'text-end'],
                ['label' => 'Variação Absoluta', 'align' => 'text-end'],
            ]">
                <tr class="align-middle">
                    <td class="text-start fw-bold text-dark">Valor Orçado (LOA)</td>
                    <td class="text-end text-secondary">R$
                        {{ number_format($resumoAnual->valor_orcado_anterior, 2, ',', '.') }}</td>
                    <td class="text-end text-dark fw-semibold">R$
                        {{ number_format($resumoAnual->valor_orcado_exercicio, 2, ',', '.') }}</td>
                    <td
                        class="text-end {{ $resumoAnual->valor_orcado_exercicio - $resumoAnual->valor_orcado_anterior >= 0 ? 'text-success' : 'text-danger' }}">
                        R$
                        {{ number_format($resumoAnual->valor_orcado_exercicio - $resumoAnual->valor_orcado_anterior, 2, ',', '.') }}
                    </td>
                </tr>
                <tr class="align-middle">
                    <td class="text-start fw-bold text-dark">Remanejamentos/Créditos Adicionais</td>
                    <td class="text-end text-secondary">R$
                        {{ number_format($resumoAnual->valor_remanejado_anterior, 2, ',', '.') }}</td>
                    <td class="text-end text-dark fw-semibold">R$
                        {{ number_format($resumoAnual->valor_remanejado_exercicio, 2, ',', '.') }}</td>
                    <td class="text-end">R$
                        {{ number_format($resumoAnual->valor_remanejado_exercicio - $resumoAnual->valor_remanejado_anterior, 2, ',', '.') }}
                    </td>
                </tr>
                <tr class="align-middle table-active-row">
                    <td class="text-start fw-bold text-primary">Valor Total Atualizado</td>
                    <td class="text-end text-secondary fw-bold">R$
                        {{ number_format($resumoAnual->valor_atualizado_anterior, 2, ',', '.') }}</td>
                    <td class="text-end text-primary fw-bold">R$
                        {{ number_format($resumoAnual->valor_atualizado_exercicio, 2, ',', '.') }}</td>
                    <td class="text-end fw-bold">R$
                        {{ number_format($resumoAnual->valor_atualizado_exercicio - $resumoAnual->valor_atualizado_anterior, 2, ',', '.') }}
                    </td>
                </tr>
                <tr class="align-middle">
                    <td class="text-start fw-bold text-dark">Valor Empenhado Líquido</td>
                    <td class="text-end text-secondary">R$
                        {{ number_format($resumoAnual->valor_empenhado_anterior, 2, ',', '.') }}</td>
                    <td class="text-end text-dark fw-semibold">R$
                        {{ number_format($resumoAnual->valor_empenhado_exercicio, 2, ',', '.') }}</td>
                    <td class="text-end">R$
                        {{ number_format($resumoAnual->valor_empenhado_exercicio - $resumoAnual->valor_empenhado_anterior, 2, ',', '.') }}
                    </td>
                </tr>
                <tr class="align-middle">
                    <td class="text-start fw-bold text-dark">Valor Pago Líquido</td>
                    <td class="text-end text-secondary">R$
                        {{ number_format($resumoAnual->valor_pago_anterior, 2, ',', '.') }}</td>
                    <td class="text-end text-dark fw-semibold">R$
                        {{ number_format($resumoAnual->valor_pago_exercicio, 2, ',', '.') }}</td>
                    <td class="text-end">R$
                        {{ number_format($resumoAnual->valor_pago_exercicio - $resumoAnual->valor_pago_anterior, 2, ',', '.') }}
                    </td>
                </tr>
            </x-tabela-transparencia>
        @endif
    </div>

    <style>
        .bg-light-gray {
            background-color: #f8f9fa;
        }

        .table-active-row {
            background-color: rgba(59, 130, 246, 0.03) !important;
        }
    </style>
@endsection

{{-- Código JavaScript isolado e corrigido --}}
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('chartEvolucaoDespesas');
            if (ctx) {
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($labelsGrafico) !!},
                        datasets: [{
                                label: 'Empenhado Anterior (' + {!! $exercicio - 1 !!} + ')',
                                data: {!! json_encode($empenhadoAnterior) !!},
                                borderColor: '#94a3b8',
                                backgroundColor: 'transparent',
                                borderWidth: 2,
                                borderDash: [5, 5],
                                tension: 0.3
                            },
                            {
                                label: 'Empenhado Exercício (' + {!! $exercicio !!} + ')',
                                data: {!! json_encode($empenhadoExercicio) !!},
                                borderColor: '#3b82f6',
                                backgroundColor: 'rgba(59, 130, 246, 0.08)',
                                fill: true,
                                borderWidth: 3,
                                tension: 0.3,
                                pointRadius: 4,
                                pointBackgroundColor: '#3b82f6'
                            }
                        ] // O colchete dos datasets fecha aqui corretamente
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: {
                                    boxWidth: 15,
                                    font: {
                                        weight: 'bold'
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return 'R$ ' + value.toLocaleString('pt-BR', {
                                            minimumFractionDigits: 0
                                        });
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
@endpush
