@extends('layouts.app')

@section('content')
    <div class="container-fluid px-lg-5 py-4 custom-dashboard-wrapper">
        <x-breadcrumb :items="$breadcrumb" />

        {{-- Cabeçalho da Página --}}
        <div
            class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3 page-header-block">
            <div>
                <h1 class="h3 fw-bold mb-1 text-heading">Indicadores Contábeis Estratégicos</h1>
                <p class="small mb-0 text-muted">Acompanhamento executivo de despesas e limites consolidados do município.
                </p>
            </div>
            <div class="exercise-selector-card p-2 rounded-3 shadow-sm border" style="min-width: 190px;">
                <form method="GET" action="{{ route('gestor.indicadores.index') }}"
                    class="d-flex align-items-center justify-content-end gap-2 w-100">
                    <label class="small fw-bold text-uppercase mb-0 text-nowrap px-1 text-secondary">Exercício:</label>
                    <input type="number" name="exercicio"
                        class="form-control border-0 fw-bold rounded-3 py-1 px-2 text-center selector-input text-primary"
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
                            <span class="text-uppercase small d-block fw-bold mb-1 text-nowrap text-muted">Comprometido
                                ({{ $exercicio }})</span>
                            <h2 class="fw-bold mb-2 fs-3 text-heading">
                                {{ number_format($pctComprometidoExercicio, 2, ',', '.') }}%</h2>
                            <div class="progress rounded-pill mb-2 custom-progress-bg" style="height: 6px;">
                                <div class="progress-bar bg-primary" role="progressbar"
                                    style="width: {{ $pctComprometidoExercicio }}%"></div>
                            </div>
                            <small class="text-nowrap d-block text-truncate text-muted">Despesa Empenhada / Total</small>
                        </div>
                    </div>
                </div>

                {{-- Card Exercício Anterior --}}
                <div class="col-md-6 col-xl-3">
                    <div class="card h-100 border-0 shadow-sm rounded-3 custom-card border-start border-secondary border-4">
                        <div class="card-body p-3 p-lg-4">
                            <span class="text-uppercase small d-block fw-bold mb-1 text-nowrap text-muted">Comprometido
                                ({{ $exercicio - 1 }})</span>
                            <h2 class="fw-bold mb-2 fs-3 text-secondary">
                                {{ number_format($pctComprometidoAnterior, 2, ',', '.') }}%</h2>
                            <div class="progress rounded-pill mb-2 custom-progress-bg" style="height: 6px;">
                                <div class="progress-bar bg-secondary" role="progressbar"
                                    style="width: {{ $pctComprometidoAnterior }}%"></div>
                            </div>
                            <small class="text-nowrap d-block text-truncate text-muted">Histórico do período
                                anterior</small>
                        </div>
                    </div>
                </div>

                {{-- Total Atualizado Disponível --}}
                <div class="col-md-6 col-xl-3">
                    <div class="card h-100 border-0 shadow-sm rounded-3 custom-card">
                        <div class="card-body p-3 p-lg-4">
                            <span class="text-uppercase small d-block fw-bold mb-1 text-nowrap text-muted">Orçamento
                                Atualizado</span>
                            <h3 class="fw-bold text-success mb-1 text-nowrap text-truncate fs-4 fs-xxl-3"
                                title="R$ {{ number_format($resumoAnual->valor_atualizado_exercicio, 2, ',', '.') }}"
                                data-bs-toggle="tooltip">
                                R$ {{ number_format($resumoAnual->valor_atualizado_exercicio, 2, ',', '.') }}
                            </h3>
                            <small class="d-block text-nowrap text-truncate mt-2 text-muted">
                                <i class="fas fa-arrow-up text-success me-1"></i> Fixado + Remanejamentos
                            </small>
                        </div>
                    </div>
                </div>

                {{-- Total Pago --}}
                <div class="col-md-6 col-xl-3">
                    <div class="card h-100 border-0 shadow-sm rounded-3 custom-card">
                        <div class="card-body p-3 p-lg-4">
                            <span class="text-uppercase small d-block fw-bold mb-1 text-nowrap text-muted">Total Pago
                                Líquido</span>
                            <h3 class="fw-bold text-info mb-1 text-nowrap text-truncate fs-4 fs-xxl-3"
                                title="R$ {{ number_format($resumoAnual->valor_pago_exercicio, 2, ',', '.') }}"
                                data-bs-toggle="tooltip">
                                R$ {{ number_format($resumoAnual->valor_pago_exercicio, 2, ',', '.') }}
                            </h3>
                            <small class="d-block text-nowrap text-truncate mt-2 text-muted">
                                <i class="fas fa-check-double text-info me-1"></i> Desembolso real
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- BLOCO GRÁFICO --}}
            <div class="card border-0 shadow-sm rounded-3 mb-4 custom-card">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3 text-heading">Evolução de Empenhos Mensais (Histórico Comparativo)</h5>
                    <div style="position: relative; height:320px; width:100%">
                        <canvas id="chartEvolucaoDespesas"></canvas>
                    </div>
                </div>
            </div>

            {{-- BLOCO TABELA --}}
            <div class="custom-table-container card border-0 shadow-sm rounded-3 p-3 custom-card">
                <x-tabela-transparencia titulo="Quadro Comparativo Consolidado das Despesas" :colunas="[
                    ['label' => 'Métrica Contábil', 'align' => 'text-start'],
                    ['label' => 'Exercício Anterior (' . ($exercicio - 1) . ')', 'align' => 'text-end'],
                    ['label' => 'Exercício Atual (' . $exercicio . ')', 'align' => 'text-end'],
                    ['label' => 'Variação Absoluta', 'align' => 'text-end'],
                ]">
                    {{-- 1. Valor Orçado --}}
                    <tr class="align-middle">
                        <td class="text-start fw-bold text-heading">Valor Orçado (LOA)</td>
                        <td class="text-end text-muted">R$
                            {{ number_format($resumoAnual->valor_orcado_anterior, 2, ',', '.') }}</td>
                        <td class="text-end text-body fw-semibold">R$
                            {{ number_format($resumoAnual->valor_orcado_exercicio, 2, ',', '.') }}</td>
                        <td
                            class="text-end fw-bold {{ $resumoAnual->valor_orcado_exercicio - $resumoAnual->valor_orcado_anterior >= 0 ? 'text-success' : 'text-danger' }}">
                            R$
                            {{ number_format($resumoAnual->valor_orcado_exercicio - $resumoAnual->valor_orcado_anterior, 2, ',', '.') }}
                        </td>
                    </tr>

                    {{-- 2. Remanejamentos --}}
                    <tr class="align-middle">
                        <td class="text-start fw-bold text-heading">Remanejamentos/Créditos Adicionais</td>
                        <td class="text-end text-muted">R$
                            {{ number_format($resumoAnual->valor_remanejado_anterior, 2, ',', '.') }}</td>
                        <td class="text-end text-body fw-semibold">R$
                            {{ number_format($resumoAnual->valor_remanejado_exercicio, 2, ',', '.') }}</td>
                        <td class="text-end fw-bold text-body">
                            R$
                            {{ number_format($resumoAnual->valor_remanejado_exercicio - $resumoAnual->valor_remanejado_anterior, 2, ',', '.') }}
                        </td>
                    </tr>

                    {{-- 3. Valor Total Atualizado --}}
                    <tr class="align-middle table-primary-light-row">
                        <td class="text-start fw-bold text-primary">Valor Total Atualizado</td>
                        <td class="text-end text-muted fw-bold">R$
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
                        <td class="text-start fw-bold text-heading">Valor Empenhado Líquido</td>
                        <td class="text-end text-muted">R$
                            {{ number_format($resumoAnual->valor_empenhado_anterior, 2, ',', '.') }}</td>
                        <td class="text-end text-body fw-semibold">R$
                            {{ number_format($resumoAnual->valor_empenhado_exercicio, 2, ',', '.') }}</td>
                        <td
                            class="text-end fw-bold {{ $resumoAnual->valor_empenhado_exercicio - $resumoAnual->valor_empenhado_anterior >= 0 ? 'text-success' : 'text-danger' }}">
                            R$
                            {{ number_format($resumoAnual->valor_empenhado_exercicio - $resumoAnual->valor_empenhado_anterior, 2, ',', '.') }}
                        </td>
                    </tr>

                    {{-- 5. Valor Pago Líquido --}}
                    <tr class="align-middle">
                        <td class="text-start fw-bold text-heading">Valor Pago Líquido</td>
                        <td class="text-end text-muted">R$
                            {{ number_format($resumoAnual->valor_pago_anterior, 2, ',', '.') }}</td>
                        <td class="text-end text-body fw-semibold">R$
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
               ESTILIZAÇÃO ESTRUTURAL (Independente de Tema - Deixa as classes cuidarem da cor)
               ========================================================================== */
        .custom-dashboard-wrapper {
            min-height: 100vh;
        }

        /* Tema Claro Nível Componente */
        .exercise-selector-card {
            background-color: #ffffff;
            border-color: #dee2e6;
        }

        .selector-input {
            background-color: #f8f9fa;
        }

        .custom-card {
            background-color: #ffffff;
        }

        .custom-progress-bg {
            background-color: #e9ecef;
        }

        .table-primary-light-row {
            background-color: rgba(13, 110, 253, 0.05);
        }

        /* Cores de Texto Semânticas Próprias para Modo Claro */
        .text-heading {
            color: #1a202c;
        }

        /* ==========================================================================
               MODO ESCURO - Sobrescreve apenas as superfícies necessárias (.dark ou mídia query)
               ========================================================================== */
        @media (prefers-color-scheme: dark) {
            .exercise-selector-card {
                background-color: #1e293b !important;
                border-color: #334155 !important;
            }

            .selector-input {
                background-color: #334155 !important;
                color: #f8fafc !important;
            }

            .custom-card {
                background-color: #1e293b !important;
            }

            .custom-progress-bg {
                background-color: #334155 !important;
            }

            .table-primary-light-row {
                background-color: rgba(13, 110, 253, 0.15);
            }

            .text-heading {
                color: #f8fafc !important;
            }
        }

        /* Mantém o suporte caso seu app chaveie adicionando a classe "dark" na tag HTML */
        html.dark .exercise-selector-card {
            background-color: #1e293b !important;
            border-color: #334155 !important;
        }

        html.dark .selector-input {
            background-color: #334155 !important;
            color: #f8fafc !important;
        }

        html.dark .custom-card {
            background-color: #1e293b !important;
        }

        html.dark .custom-progress-bg {
            background-color: #334155 !important;
        }

        html.dark .table-primary-light-row {
            background-color: rgba(13, 110, 253, 0.15);
        }

        html.dark .text-heading {
            color: #f8fafc !important;
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

                const textColor = isDarkMode ? '#94a3b8' : '#64748b';
                const gridColor = isDarkMode ? '#334155' : '#e2e8f0';

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($labelsGrafico) !!},
                        datasets: [{
                                label: 'Empenhado Anterior (' + {!! $exercicio - 1 !!} + ')',
                                data: {!! json_encode($empenhadoAnterior) !!},
                                borderColor: isDarkMode ? '#64748b' : '#94a3b8',
                                backgroundColor: 'transparent',
                                borderWidth: 2,
                                borderDash: [5, 5],
                                tension: 0.3
                            },
                            {
                                label: 'Empenhado Exercício (' + {!! $exercicio !!} + ')',
                                data: {!! json_encode($empenhadoExercicio) !!},
                                borderColor: isDarkMode ? '#38bdf8' : '#0284c7',
                                backgroundColor: isDarkMode ? 'rgba(56, 189, 248, 0.12)' :
                                    'rgba(2, 132, 199, 0.06)',
                                fill: true,
                                borderWidth: 3,
                                tension: 0.3,
                                pointRadius: 4,
                                pointBackgroundColor: isDarkMode ? '#38bdf8' : '#0284c7'
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
