@extends('layouts.app')

@section('content')
    <div class="container-fluid px-lg-5 py-4 custom-dashboard-wrapper">
        <x-breadcrumb :items="$breadcrumb" />

        {{-- Cabeçalho da Página --}}
        <div
            class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3 page-header-block">
            <div>
                <h1 class="h3 fw-bold dashboard-title mb-1">Indicadores Contábeis Estratégicos</h1>
                <p class="dashboard-subtitle small mb-0">Acompanhamento executivo de despesas e limites consolidados do
                    município.</p>
            </div>
            <div class="exercise-selector-card p-2 rounded-3 shadow-sm border" style="min-width: 190px;">
                <form method="GET" action="{{ route('gestor.indicadores.index') }}"
                    class="d-flex align-items-center justify-content-end gap-2 w-100">
                    <label class="small fw-bold text-uppercase mb-0 text-nowrap px-1 selector-label">Exercício:</label>
                    <input type="number" name="exercicio"
                        class="form-control border-0 fw-bold rounded-3 py-1 px-2 text-center selector-input"
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
            {{-- CARDS SUPERIORES --}}
            <div class="row g-3 mb-4">
                {{-- Card Exercício Atual --}}
                <div class="col-md-6 col-xl-3">
                    <div class="card h-100 border-0 shadow-sm rounded-3 custom-card border-start border-primary border-4">
                        <div class="card-body p-3 p-lg-4">
                            <span
                                class="card-metric-label text-uppercase small d-block fw-bold mb-1 text-nowrap">Comprometido
                                ({{ $exercicio }})</span>
                            <h2 class="fw-bold card-metric-value mb-2 fs-3">
                                {{ number_format($pctComprometidoExercicio, 2, ',', '.') }}%</h2>
                            <div class="progress rounded-pill mb-2 custom-progress-bg" style="height: 6px;">
                                <div class="progress-bar bg-primary" role="progressbar"
                                    style="width: {{ $pctComprometidoExercicio }}%"></div>
                            </div>
                            <small class="card-metric-sub text-nowrap d-block text-truncate">Despesa Empenhada /
                                Total</small>
                        </div>
                    </div>
                </div>

                {{-- Card Exercício Anterior --}}
                <div class="col-md-6 col-xl-3">
                    <div class="card h-100 border-0 shadow-sm rounded-3 custom-card border-start border-secondary border-4">
                        <div class="card-body p-3 p-lg-4">
                            <span
                                class="card-metric-label text-uppercase small d-block fw-bold mb-1 text-nowrap">Comprometido
                                ({{ $exercicio - 1 }})</span>
                            <h2 class="fw-bold card-metric-value-secondary mb-2 fs-3">
                                {{ number_format($pctComprometidoAnterior, 2, ',', '.') }}%</h2>
                            <div class="progress rounded-pill mb-2 custom-progress-bg" style="height: 6px;">
                                <div class="progress-bar bg-secondary" role="progressbar"
                                    style="width: {{ $pctComprometidoAnterior }}%"></div>
                            </div>
                            <small class="card-metric-sub text-nowrap d-block text-truncate">Histórico do período
                                anterior</small>
                        </div>
                    </div>
                </div>

                {{-- Total Atualizado Disponível --}}
                <div class="col-md-6 col-xl-3">
                    <div class="card h-100 border-0 shadow-sm rounded-3 custom-card">
                        <div class="card-body p-3 p-lg-4">
                            <span class="card-metric-label text-uppercase small d-block fw-bold mb-1 text-nowrap">Orçamento
                                Atualizado</span>
                            <h3 class="fw-bold text-success mb-1 text-nowrap text-truncate fs-4 fs-xxl-3"
                                title="R$ {{ number_format($resumoAnual->valor_atualizado_exercicio, 2, ',', '.') }}"
                                data-bs-toggle="tooltip">
                                R$ {{ number_format($resumoAnual->valor_atualizado_exercicio, 2, ',', '.') }}
                            </h3>
                            <small class="card-metric-sub d-block text-nowrap text-truncate mt-2">
                                <i class="fas fa-arrow-up text-success me-1"></i> Fixado + Remanejamentos
                            </small>
                        </div>
                    </div>
                </div>

                {{-- Total Pago --}}
                <div class="col-md-6 col-xl-3">
                    <div class="card h-100 border-0 shadow-sm rounded-3 custom-card">
                        <div class="card-body p-3 p-lg-4">
                            <span class="card-metric-label text-uppercase small d-block fw-bold mb-1 text-nowrap">Total Pago
                                Líquido</span>
                            <h3 class="fw-bold card-metric-info mb-1 text-nowrap text-truncate fs-4 fs-xxl-3"
                                title="R$ {{ number_format($resumoAnual->valor_pago_exercicio, 2, ',', '.') }}"
                                data-bs-toggle="tooltip">
                                R$ {{ number_format($resumoAnual->valor_pago_exercicio, 2, ',', '.') }}
                            </h3>
                            <small class="card-metric-sub d-block text-nowrap text-truncate mt-2">
                                <i class="fas fa-check-double card-metric-info me-1"></i> Desembolso real
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- BLOCO GRÁFICO --}}
            <div class="card border-0 shadow-sm rounded-3 mb-4 custom-card">
                <div class="card-body p-4">
                    <h5 class="fw-bold graph-title mb-3">Evolução de Empenhos Mensais (Histórico Comparativo)</h5>
                    <div style="position: relative; height:320px; width:100%">
                        <canvas id="chartEvolucaoDespesas"></canvas>
                    </div>
                </div>
            </div>

            {{-- BLOCO TABELA --}}
            <div class="custom-table-container">
                <x-tabela-transparencia titulo="Quadro Comparativo Consolidado das Despesas" :colunas="[
                    ['label' => 'Métrica Contábil', 'align' => 'text-start'],
                    ['label' => 'Exercício Anterior (' . ($exercicio - 1) . ')', 'align' => 'text-end'],
                    ['label' => 'Exercício Atual (' . $exercicio . ')', 'align' => 'text-end'],
                    ['label' => 'Variação Absoluta', 'align' => 'text-end'],
                ]">
                    {{-- 1. Valor Orçado --}}
                    <tr class="align-middle">
                        <td class="text-start fw-bold row-title">Valor Orçado (LOA)</td>
                        <td class="text-end row-value-muted">R$
                            {{ number_format($resumoAnual->valor_orcado_anterior, 2, ',', '.') }}</td>
                        <td class="text-end row-value-highlight">R$
                            {{ number_format($resumoAnual->valor_orcado_exercicio, 2, ',', '.') }}</td>
                        <td
                            class="text-end fw-bold {{ $resumoAnual->valor_orcado_exercicio - $resumoAnual->valor_orcado_anterior >= 0 ? 'text-success' : 'text-danger' }}">
                            R$
                            {{ number_format($resumoAnual->valor_orcado_exercicio - $resumoAnual->valor_orcado_anterior, 2, ',', '.') }}
                        </td>
                    </tr>

                    {{-- 2. Remanejamentos --}}
                    <tr class="align-middle">
                        <td class="text-start fw-bold row-title">Remanejamentos/Créditos Adicionais</td>
                        <td class="text-end row-value-muted">R$
                            {{ number_format($resumoAnual->valor_remanejado_anterior, 2, ',', '.') }}</td>
                        <td class="text-end row-value-highlight">R$
                            {{ number_format($resumoAnual->valor_remanejado_exercicio, 2, ',', '.') }}</td>
                        <td class="text-end fw-bold row-value-highlight">
                            R$
                            {{ number_format($resumoAnual->valor_remanejado_exercicio - $resumoAnual->valor_remanejado_anterior, 2, ',', '.') }}
                        </td>
                    </tr>

                    {{-- 3. Valor Total Atualizado --}}
                    <tr class="align-middle custom-table-active-row">
                        <td class="text-start fw-bold text-primary">Valor Total Atualizado</td>
                        <td class="text-end row-value-muted fw-bold">R$
                            {{ number_format($resumoAnual->valor_atualizado_anterior, 2, ',', '.') }}</td>
                        <td class="text-end text-primary fw-bold">R$
                            {{ number_format($resumoAnual->valor_atualizado_exercicio, 2, ',', '.') }}</td>
                        <td
                            class="text-end fw-bold {{ $resumoAnual->valor_atualizado_exercicio - $resumoAnual->valor_atualizado_anterior >= 0 ? 'text-success' : 'text-danger' }}">
                            R$
                            {{ number_format($resumoAnual->valor_atualizado_exercicio - $resumoAnual->valor_atualizado_anterior, 2, ',', '.') }}
                        </td>
                    </tr>

                    {{-- 4. Valor Empenhado Líquido --}}
                    <tr class="align-middle">
                        <td class="text-start fw-bold row-title">Valor Empenhado Líquido</td>
                        <td class="text-end row-value-muted">R$
                            {{ number_format($resumoAnual->valor_empenhado_anterior, 2, ',', '.') }}</td>
                        <td class="text-end row-value-highlight">R$
                            {{ number_format($resumoAnual->valor_empenhado_exercicio, 2, ',', '.') }}</td>
                        <td
                            class="text-end fw-bold {{ $resumoAnual->valor_empenhado_exercicio - $resumoAnual->valor_empenhado_anterior >= 0 ? 'text-success' : 'text-danger' }}">
                            R$
                            {{ number_format($resumoAnual->valor_empenhado_exercicio - $resumoAnual->valor_empenhado_anterior, 2, ',', '.') }}
                        </td>
                    </tr>

                    {{-- 5. Valor Pago Líquido --}}
                    <tr class="align-middle">
                        <td class="text-start fw-bold row-title">Valor Pago Líquido</td>
                        <td class="text-end row-value-muted">R$
                            {{ number_format($resumoAnual->valor_pago_anterior, 2, ',', '.') }}</td>
                        <td class="text-end row-value-highlight">R$
                            {{ number_format($resumoAnual->valor_pago_exercicio, 2, ',', '.') }}</td>
                        <td
                            class="text-end fw-bold {{ $resumoAnual->valor_pago_exercicio - $resumoAnual->valor_pago_anterior >= 0 ? 'text-success' : 'text-danger' }}">
                            R$
                            {{ number_format($resumoAnual->valor_pago_exercicio - $resumoAnual->valor_pago_anterior, 2, ',', '.') }}
                        </td>
                    </tr>
                </x-tabela-transparencia>
            </div>
        @endif
    </div>

    <style>
        /* ==========================================================================
               DEFINIÇÃO DO MODO CLARO (PADRÃO) - Baseado estritamente no seu print claro
               ========================================================================== */
        .custom-dashboard-wrapper {
            background-color: #f8f9fa !important;
            color: #212529;
        }

        .dashboard-title {
            color: #1f2937 !important;
        }

        .dashboard-subtitle {
            color: #6b7280 !important;
        }

        .exercise-selector-card {
            background-color: #ffffff !important;
            border-color: #e5e7eb !important;
        }

        .selector-label {
            color: #4b5563 !important;
        }

        .selector-input {
            background-color: #f3f4f6 !important;
            color: #3b82f6 !important;
        }

        .custom-card {
            background-color: #ffffff !important;
            color: #212529 !important;
        }

        .card-metric-label {
            color: #6b7280 !important;
        }

        .card-metric-value {
            color: #111827 !important;
        }

        .card-metric-value-secondary {
            color: #4b5563 !important;
        }

        .card-metric-sub {
            color: #6b7280 !important;
        }

        .card-metric-info {
            color: #06b6d4 !important;
        }

        .custom-progress-bg {
            background-color: #e5e7eb !important;
        }

        .graph-title {
            color: #111827 !important;
        }

        .custom-table-container table {
            color: #212529 !important;
        }

        .row-title {
            color: #1f2937 !important;
        }

        .row-value-muted {
            color: #6b7280 !important;
        }

        .row-value-highlight {
            color: #111827 !important;
        }

        .custom-table-active-row {
            background-color: rgba(59, 130, 246, 0.04) !important;
        }

        /* ==========================================================================
               MODO ESCURO DINÂMICO (Sistema ou Classe .dark) - Baseado no seu print escuro
               ========================================================================== */
        @media (prefers-color-scheme: dark) {

            body,
            .custom-dashboard-wrapper {
                background-color: #111827 !important;
                color: #f3f4f6 !important;
            }

            .dashboard-title {
                color: #ffffff !important;
            }

            .dashboard-subtitle {
                color: #9ca3af !important;
            }

            .exercise-selector-card {
                background-color: #1f2937 !important;
                border-color: #374151 !important;
            }

            .selector-label {
                color: #9ca3af !important;
            }

            .selector-input {
                background-color: #374151 !important;
                color: #60a5fa !important;
            }

            .custom-card {
                background-color: #1f2937 !important;
                color: #f3f4f6 !important;
            }

            .card-metric-label {
                color: #9ca3af !important;
            }

            .card-metric-value {
                color: #ffffff !important;
            }

            .card-metric-value-secondary {
                color: #9ca3af !important;
            }

            .card-metric-sub {
                color: #9ca3af !important;
            }

            .card-metric-info {
                color: #22d3ee !important;
            }

            .custom-progress-bg {
                background-color: #374151 !important;
            }

            .graph-title {
                color: #ffffff !important;
            }

            .custom-table-container table {
                color: #f3f4f6 !important;
            }

            .row-title {
                color: #ffffff !important;
            }

            .row-value-muted {
                color: #9ca3af !important;
            }

            .row-value-highlight {
                color: #ffffff !important;
            }

            .custom-table-active-row {
                background-color: rgba(96, 165, 251, 0.1) !important;
            }

            .custom-table-container thead th {
                background-color: #374151 !important;
                color: #ffffff !important;
            }
        }

        /* Espelhamento para acionadores manuais por classe html */
        html.dark .custom-dashboard-wrapper {
            background-color: #111827 !important;
            color: #f3f4f6 !important;
        }

        html.dark .dashboard-title {
            color: #ffffff !important;
        }

        html.dark .dashboard-subtitle {
            color: #9ca3af !important;
        }

        html.dark .exercise-selector-card {
            background-color: #1f2937 !important;
            border-color: #374151 !important;
        }

        html.dark .selector-label {
            color: #9ca3af !important;
        }

        html.dark .selector-input {
            background-color: #374151 !important;
            color: #60a5fa !important;
        }

        html.dark .custom-card {
            background-color: #1f2937 !important;
            color: #f3f4f6 !important;
        }

        html.dark .card-metric-label {
            color: #9ca3af !important;
        }

        html.dark .card-metric-value {
            color: #ffffff !important;
        }

        html.dark .card-metric-value-secondary {
            color: #9ca3af !important;
        }

        html.dark .card-metric-sub {
            color: #9ca3af !important;
        }

        html.dark .card-metric-info {
            color: #22d3ee !important;
        }

        html.dark .custom-progress-bg {
            background-color: #374151 !important;
        }

        html.dark .graph-title {
            color: #ffffff !important;
        }

        html.dark .custom-table-container table {
            color: #f3f4f6 !important;
        }

        html.dark .row-title {
            color: #ffffff !important;
        }

        html.dark .row-value-muted {
            color: #9ca3af !important;
        }

        html.dark .row-value-highlight {
            color: #ffffff !important;
        }

        html.dark .custom-table-active-row {
            background-color: rgba(96, 165, 251, 0.1) !important;
        }
    </style>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('chartEvolucaoDespesas');
            if (ctx) {
                const isDarkMode = window.matchMedia('(prefers-color-scheme: dark)').matches ||
                    document.documentElement.classList.contains('dark');

                const textColor = isDarkMode ? '#9ca3af' : '#4b5563';
                const gridColor = isDarkMode ? '#374151' : '#e5e7eb';

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($labelsGrafico) !!},
                        datasets: [{
                                label: 'Empenhado Anterior (' + {!! $exercicio - 1 !!} + ')',
                                data: {!! json_encode($empenhadoAnterior) !!},
                                borderColor: isDarkMode ? '#6b7280' : '#94a3b8',
                                backgroundColor: 'transparent',
                                borderWidth: 2,
                                borderDash: [5, 5],
                                tension: 0.3
                            },
                            {
                                label: 'Empenhado Exercício (' + {!! $exercicio !!} + ')',
                                data: {!! json_encode($empenhadoExercicio) !!},
                                borderColor: isDarkMode ? '#60a5fa' : '#3b82f6',
                                backgroundColor: isDarkMode ? 'rgba(96, 165, 251, 0.15)' :
                                    'rgba(59, 130, 246, 0.08)',
                                fill: true,
                                borderWidth: 3,
                                tension: 0.3,
                                pointRadius: 4,
                                pointBackgroundColor: isDarkMode ? '#60a5fa' : '#3b82f6'
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top',
                                labels: {
                                    boxWidth: 15,
                                    color: textColor,
                                    font: {
                                        weight: 'bold'
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    color: gridColor
                                },
                                ticks: {
                                    color: textColor
                                }
                            },
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: gridColor
                                },
                                ticks: {
                                    color: textColor,
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

            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        });
    </script>
@endpush
