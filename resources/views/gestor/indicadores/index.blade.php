@extends('layouts.app')

@section('content')
    <div class="container-fluid px-lg-5 py-4 bg-light-gray min-vh-100 dark-mode-container">
        <x-breadcrumb :items="$breadcrumb" />

        {{-- Cabeçalho da Página com seletor de Exercício --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <div>
                <h1 class="h3 fw-bold text-dark dark-text-white mb-1">Indicadores Contábeis Estratégicos</h1>
                <p class="text-muted dark-text-muted small mb-0">Acompanhamento executivo de despesas e limites consolidados
                    do município.</p>
            </div>
            <div class="bg-white dark-bg-card p-2 rounded-3 shadow-sm border dark-border-card" style="min-width: 190px;">
                <form method="GET" action="{{ route('gestor.indicadores.index') }}"
                    class="d-flex align-items-center justify-content-end gap-2 w-100">
                    <label
                        class="small fw-bold text-secondary dark-text-muted text-uppercase mb-0 text-nowrap px-1">Exercício:</label>
                    <input type="number" name="exercicio"
                        class="form-control border-0 bg-light dark-bg-input fw-bold text-primary dark-text-primary rounded-3 py-1 px-2 text-center"
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
            <div class="row g-3 mb-4">
                {{-- Card Exercício Atual --}}
                <div class="col-md-6 col-xl-3">
                    <div
                        class="card h-100 border-0 shadow-sm rounded-3 bg-white dark-bg-card border-start border-primary border-4">
                        <div class="card-body p-3 p-lg-4">
                            <span
                                class="text-muted dark-text-muted text-uppercase small d-block fw-bold mb-1 text-nowrap">Comprometido
                                ({{ $exercicio }})</span>
                            <h2 class="fw-bold text-dark dark-text-white mb-2 fs-3">
                                {{ number_format($pctComprometidoExercicio, 2, ',', '.') }}%</h2>
                            <div class="progress rounded-pill mb-2 bg-light dark-bg-input" style="height: 6px;">
                                <div class="progress-bar bg-primary" role="progressbar"
                                    style="width: {{ $pctComprometidoExercicio }}%"></div>
                            </div>
                            <small class="text-muted dark-text-muted text-nowrap d-block text-truncate">Despesa Empenhada /
                                Total</small>
                        </div>
                    </div>
                </div>

                {{-- Card Exercício Anterior --}}
                <div class="col-md-6 col-xl-3">
                    <div
                        class="card h-100 border-0 shadow-sm rounded-3 bg-white dark-bg-card border-start border-secondary border-4">
                        <div class="card-body p-3 p-lg-4">
                            <span
                                class="text-muted dark-text-muted text-uppercase small d-block fw-bold mb-1 text-nowrap">Comprometido
                                ({{ $exercicio - 1 }})</span>
                            <h2 class="fw-bold text-secondary dark-text-muted mb-2 fs-3">
                                {{ number_format($pctComprometidoAnterior, 2, ',', '.') }}%</h2>
                            <div class="progress rounded-pill mb-2 bg-light dark-bg-input" style="height: 6px;">
                                <div class="progress-bar bg-secondary" role="progressbar"
                                    style="width: {{ $pctComprometidoAnterior }}%"></div>
                            </div>
                            <small class="text-muted dark-text-muted text-nowrap d-block text-truncate">Histórico do período
                                anterior</small>
                        </div>
                    </div>
                </div>

                {{-- Total Atualizado Disponível --}}
                <div class="col-md-6 col-xl-3">
                    <div class="card h-100 border-0 shadow-sm rounded-3 bg-white dark-bg-card">
                        <div class="card-body p-3 p-lg-4">
                            <span
                                class="text-muted dark-text-muted text-uppercase small d-block fw-bold mb-1 text-nowrap">Orçamento
                                Atualizado</span>
                            <h3 class="fw-bold text-success mb-1 text-nowrap text-truncate fs-4 fs-xxl-3"
                                title="R$ {{ number_format($resumoAnual->valor_atualizado_exercicio, 2, ',', '.') }}"
                                data-bs-toggle="tooltip">
                                R$ {{ number_format($resumoAnual->valor_atualizado_exercicio, 2, ',', '.') }}
                            </h3>
                            <small class="text-muted dark-text-muted d-block text-nowrap text-truncate mt-2">
                                <i class="fas fa-arrow-up text-success me-1"></i> Fixado + Remanejamentos
                            </small>
                        </div>
                    </div>
                </div>

                {{-- Total Pago --}}
                <div class="col-md-6 col-xl-3">
                    <div class="card h-100 border-0 shadow-sm rounded-3 bg-white dark-bg-card">
                        <div class="card-body p-3 p-lg-4">
                            <span
                                class="text-muted dark-text-muted text-uppercase small d-block fw-bold mb-1 text-nowrap">Total
                                Pago Líquido</span>
                            <h3 class="fw-bold text-info dark-text-info mb-1 text-nowrap text-truncate fs-4 fs-xxl-3"
                                title="R$ {{ number_format($resumoAnual->valor_pago_exercicio, 2, ',', '.') }}"
                                data-bs-toggle="tooltip">
                                R$ {{ number_format($resumoAnual->valor_pago_exercicio, 2, ',', '.') }}
                            </h3>
                            <small class="text-muted dark-text-muted d-block text-nowrap text-truncate mt-2">
                                <i class="fas fa-check-double text-info dark-text-info me-1"></i> Desembolso real
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- BLOCO GRÁFICO: Evolução Mensal da Despesa Empenhada --}}
            <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white dark-bg-card">
                <div class="card-body p-4">
                    <h5 class="fw-bold text-dark dark-text-white mb-3">Evolução de Empenhos Mensais (Histórico Compartativo)
                    </h5>
                    <div style="position: relative; height:320px; width:100%">
                        <canvas id="chartEvolucaoDespesas"></canvas>
                    </div>
                </div>
            </div>

            {{-- BLOCO TABELA: Detalhamento Anualizado Geral (100% Corrigido para Modo Claro e Escuro) --}}
            <div class="dark-table-wrapper">
                <x-tabela-transparencia titulo="Quadro Comparativo Consolidado das Despesas" :colunas="[
                    ['label' => 'Métrica Contábil', 'align' => 'text-start'],
                    ['label' => 'Exercício Anterior (' . ($exercicio - 1) . ')', 'align' => 'text-end'],
                    ['label' => 'Exercício Atual (' . $exercicio . ')', 'align' => 'text-end'],
                    ['label' => 'Variação Absoluta', 'align' => 'text-end'],
                ]">
                    {{-- 1. Valor Orçado --}}
                    <tr class="align-middle">
                        <td class="text-start fw-bold text-dark dark-text-white">Valor Orçado (LOA)</td>
                        <td class="text-end text-secondary dark-text-muted">R$
                            {{ number_format($resumoAnual->valor_orcado_anterior, 2, ',', '.') }}</td>
                        <td class="text-end text-dark dark-text-white fw-semibold">R$
                            {{ number_format($resumoAnual->valor_orcado_exercicio, 2, ',', '.') }}</td>
                        <td
                            class="text-end {{ $resumoAnual->valor_orcado_exercicio - $resumoAnual->valor_orcado_anterior >= 0 ? 'text-success' : 'text-danger' }}">
                            R$
                            {{ number_format($resumoAnual->valor_orcado_exercicio - $resumoAnual->valor_orcado_anterior, 2, ',', '.') }}
                        </td>
                    </tr>

                    {{-- 2. Remanejamentos --}}
                    <tr class="align-middle">
                        <td class="text-start fw-bold text-dark dark-text-white">Remanejamentos/Créditos Adicionais</td>
                        <td class="text-end text-secondary dark-text-muted">R$
                            {{ number_format($resumoAnual->valor_remanejado_anterior, 2, ',', '.') }}</td>
                        <td class="text-end text-dark dark-text-white fw-semibold">R$
                            {{ number_format($resumoAnual->valor_remanejado_exercicio, 2, ',', '.') }}</td>
                        <td class="text-end dark-text-white">
                            R$
                            {{ number_format($resumoAnual->valor_remanejado_exercicio - $resumoAnual->valor_remanejado_anterior, 2, ',', '.') }}
                        </td>
                    </tr>

                    {{-- 3. Valor Total Atualizado --}}
                    <tr class="align-middle table-active-row dark-table-active">
                        <td class="text-start fw-bold text-primary dark-text-primary">Valor Total Atualizado</td>
                        <td class="text-end text-secondary dark-text-muted fw-bold">R$
                            {{ number_format($resumoAnual->valor_atualizado_anterior, 2, ',', '.') }}</td>
                        <td class="text-end text-primary dark-text-primary fw-bold">R$
                            {{ number_format($resumoAnual->valor_atualizado_exercicio, 2, ',', '.') }}</td>
                        <td
                            class="text-end fw-bold {{ $resumoAnual->valor_atualizado_exercicio - $resumoAnual->valor_atualizado_anterior >= 0 ? 'text-success' : 'text-danger' }}">
                            R$
                            {{ number_format($resumoAnual->valor_atualizado_exercicio - $resumoAnual->valor_atualizado_anterior, 2, ',', '.') }}
                        </td>
                    </tr>

                    {{-- 4. Valor Empenhado Líquido --}}
                    <tr class="align-middle">
                        <td class="text-start fw-bold text-dark dark-text-white">Valor Empenhado Líquido</td>
                        <td class="text-end text-secondary dark-text-muted">R$
                            {{ number_format($resumoAnual->valor_empenhado_anterior, 2, ',', '.') }}</td>
                        <td class="text-end text-dark dark-text-white fw-semibold">R$
                            {{ number_format($resumoAnual->valor_empenhado_exercicio, 2, ',', '.') }}</td>
                        <td
                            class="text-end {{ $resumoAnual->valor_empenhado_exercicio - $resumoAnual->valor_empenhado_anterior >= 0 ? 'text-success' : 'text-danger' }}">
                            R$
                            {{ number_format($resumoAnual->valor_empenhado_exercicio - $resumoAnual->valor_empenhado_anterior, 2, ',', '.') }}
                        </td>
                    </tr>

                    {{-- 5. Valor Pago Líquido --}}
                    <tr class="align-middle">
                        <td class="text-start fw-bold text-dark dark-text-white">Valor Pago Líquido</td>
                        <td class="text-end text-secondary dark-text-muted">R$
                            {{ number_format($resumoAnual->valor_pago_anterior, 2, ',', '.') }}</td>
                        <td class="text-end text-dark dark-text-white fw-semibold">R$
                            {{ number_format($resumoAnual->valor_pago_exercicio, 2, ',', '.') }}</td>
                        <td
                            class="text-end {{ $resumoAnual->valor_pago_exercicio - $resumoAnual->valor_pago_anterior >= 0 ? 'text-success' : 'text-danger' }}">
                            R$
                            {{ number_format($resumoAnual->valor_pago_exercicio - $resumoAnual->valor_pago_anterior, 2, ',', '.') }}
                        </td>
                    </tr>
                </x-tabela-transparencia>
            </div>
        @endif
    </div>

    <style>
        .bg-light-gray {
            background-color: #f8f9fa;
        }

        .table-active-row {
            background-color: rgba(59, 130, 246, 0.03) !important;
        }

        /* Regras customizadas para injeção de Modo Escuro Dinâmico */
        @media (prefers-color-scheme: dark) {

            body,
            .dark-mode-container {
                background-color: #111827 !important;
                color: #f3f4f6;
            }

            .dark-bg-card {
                background-color: #1f2937 !important;
                color: #f3f4f6 !important;
            }

            .dark-border-card {
                border-color: #374151 !important;
            }

            .dark-bg-input {
                background-color: #374151 !important;
                color: #f3f4f6 !important;
            }

            .dark-text-white {
                color: #ffffff !important;
            }

            .dark-text-muted {
                color: #9ca3af !important;
            }

            .dark-text-primary {
                color: #60a5fa !important;
            }

            .dark-text-info {
                color: #22d3ee !important;
            }

            .dark-table-active {
                background-color: rgba(96, 165, 251, 0.1) !important;
            }

            /* Ajuste de contraste para textos de tabelas injetadas */
            .dark-table-wrapper table {
                color: #f3f4f6 !important;
            }

            .dark-table-wrapper thead th {
                background-color: #374151 !important;
                color: #ffffff !important;
            }
        }

        /* Caso seu sistema use uma classe nativa no HTML (<html class="dark">) para o Dark Mode */
        html.dark .dark-mode-container {
            background-color: #111827 !important;
        }

        html.dark .dark-bg-card {
            background-color: #1f2937 !important;
            color: #f3f4f6 !important;
        }

        html.dark .dark-border-card {
            border-color: #374151 !important;
        }

        html.dark .dark-bg-input {
            background-color: #374151 !important;
            color: #f3f4f6 !important;
        }

        html.dark .dark-text-white {
            color: #ffffff !important;
        }

        html.dark .dark-text-muted {
            color: #9ca3af !important;
        }

        html.dark .dark-text-primary {
            color: #60a5fa !important;
        }

        html.dark .dark-text-info {
            color: #22d3ee !important;
        }

        html.dark .dark-table-active {
            background-color: rgba(96, 165, 251, 0.1) !important;
        }

        html.dark .dark-table-wrapper table {
            color: #f3f4f6 !important;
        }
    </style>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('chartEvolucaoDespesas');
            if (ctx) {
                // Detecta se a interface está em modo escuro para adaptar as cores das fontes e grids do gráfico
                const isDarkMode = window.matchMedia('(prefers-color-scheme: dark)').matches || document
                    .documentElement.classList.contains('dark');

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

            // Inicializa Tooltips do Bootstrap se houverem
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        });
    </script>
@endpush
