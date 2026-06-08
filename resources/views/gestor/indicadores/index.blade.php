@extends('layouts.app')

@section('content')
    <div class="container-fluid px-lg-5 py-4 page-content min-vh-100">
        <x-breadcrumb :items="$breadcrumb" />

        {{-- Cabeçalho da Página com seletor de Exercício --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <div>
                <h1 class="h3 fw-bold text-dark mb-1">Painel Estratégico de BI Contábil</h1>
                <p class="text-muted small mb-0">Visão analítica de inteligência para tomada de decisão imediata.</p>
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
            <div class="row g-3 mb-4">
                <div class="col-md-6 col-xl-3">
                    <div class="card h-100 border-0 shadow-sm rounded-3 bg-white border-start border-primary border-4">
                        <div class="card-body p-3 p-lg-4">
                            <span class="text-muted text-uppercase small d-block fw-bold mb-1 text-nowrap">Comprometido
                                ({{ $exercicio }})</span>
                            <h2 class="fw-bold text-dark mb-2 fs-3">
                                {{ number_format($pctComprometidoExercicio, 2, ',', '.') }}%</h2>
                            <div class="progress rounded-pill mb-2" style="height: 6px;">
                                <div class="progress-bar bg-primary" role="progressbar"
                                    style="width: {{ $pctComprometidoExercicio }}%"></div>
                            </div>
                            <small class="text-muted text-nowrap d-block text-truncate">Despesa Empenhada / Total</small>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-xl-3">
                    <div class="card h-100 border-0 shadow-sm rounded-3 bg-white border-start border-secondary border-4">
                        <div class="card-body p-3 p-lg-4">
                            <span class="text-muted text-uppercase small d-block fw-bold mb-1 text-nowrap">Comprometido
                                ({{ $exercicio - 1 }})</span>
                            <h2 class="fw-bold text-secondary mb-2 fs-3">
                                {{ number_format($pctComprometidoAnterior, 2, ',', '.') }}%</h2>
                            <div class="progress rounded-pill mb-2" style="height: 6px;">
                                <div class="progress-bar bg-secondary" role="progressbar"
                                    style="width: {{ $pctComprometidoAnterior }}%"></div>
                            </div>
                            <small class="text-muted text-nowrap d-block text-truncate">Histórico do período
                                anterior</small>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-xl-3">
                    <div class="card h-100 border-0 shadow-sm rounded-3 bg-white">
                        <div class="card-body p-3 p-lg-4">
                            <span class="text-muted text-uppercase small d-block fw-bold mb-1 text-nowrap">Orçamento
                                Atualizado</span>
                            <h3 class="fw-bold text-success mb-1 text-nowrap text-truncate fs-4 fs-xxl-3"
                                title="R$ {{ number_format($resumoAnual->valor_atualizado_exercicio, 2, ',', '.') }}"
                                data-bs-toggle="tooltip">
                                R$ {{ number_format($resumoAnual->valor_atualizado_exercicio, 2, ',', '.') }}
                            </h3>
                            <small class="text-muted d-block text-nowrap text-truncate mt-2">
                                <i class="fas fa-arrow-up text-success me-1"></i> Fixado + Remanejamentos
                            </small>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-xl-3">
                    <div class="card h-100 border-0 shadow-sm rounded-3 bg-white">
                        <div class="card-body p-3 p-lg-4">
                            <span class="text-muted text-uppercase small d-block fw-bold mb-1 text-nowrap">Total Pago
                                Líquido</span>
                            <h3 class="fw-bold text-info mb-1 text-nowrap text-truncate fs-4 fs-xxl-3"
                                title="R$ {{ number_format($resumoAnual->valor_pago_exercicio, 2, ',', '.') }}"
                                data-bs-toggle="tooltip">
                                R$ {{ number_format($resumoAnual->valor_pago_exercicio, 2, ',', '.') }}
                            </h3>
                            <small class="text-muted d-block text-nowrap text-truncate mt-2">
                                <i class="fas fa-check-double text-info me-1"></i> Desembolso real
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SEÇÃO DE INTAKES & GRÁFICOS DE BI --}}
            <div class="row g-4 mb-4">
                {{-- Donut Chart: Distribuição de Despesas por Função --}}
                <div class="col-lg-5">
                    <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
                        <div class="card-body p-4 d-flex flex-column">
                            <h5 class="fw-bold text-dark mb-1">Composição por Função</h5>
                            <p class="text-muted small mb-3">Onde os recursos estão concentrados neste exercício.</p>
                            <div class="my-auto d-flex justify-content-center align-items-center"
                                style="position: relative; height:240px;">
                                <canvas id="chartBIFuncoes"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Horizontal Bar Chart: Maiores Gastadores (Top 5 Unidades) --}}
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm rounded-3 h-100 bg-white">
                        <div class="card-body p-4 d-flex flex-column">
                            <h5 class="fw-bold text-dark mb-1">Top 5 Unidades Mais Demandantes</h5>
                            <p class="text-muted small mb-3">Secretarias ou fundos com maior volume de empenhos acumulados.
                            </p>
                            <div class="my-auto" style="position: relative; height:240px; width:100%">
                                <canvas id="chartBIUnidades"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- BLOCO GRÁFICO EVOLUTIVO --}}
            <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
                <div class="card-body p-4">
                    <h5 class="fw-bold text-dark mb-3">Evolução de Empenhos Mensais (Histórico Comparativo)</h5>
                    <div style="position: relative; height:300px; width:100%">
                        <canvas id="chartEvolucaoDespesas"></canvas>
                    </div>
                </div>
            </div>

            <div class="row">
                @foreach (['chartUnidades', 'chartFuncoes', 'chartSubfuncoes', 'chartElementos', 'chartRecursos'] as $id)
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-body" style="height: 350px;">
                                <canvas id="{{ $id }}"></canvas>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- BLOCO DE ABAS DETALHADAS --}}
            <div class="card border-0 shadow-sm rounded-3 mb-4 bg-white">
                <div class="card-body p-4">
                    <h5 class="fw-bold text-dark mb-3">Detalhamento das Despesas por Categorias</h5>

                    <ul class="nav nav-tabs border-bottom mb-3" id="abasIndicadores" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active fw-bold small text-uppercase py-2.5" id="unidades-tab"
                                data-bs-toggle="tab" data-bs-target="#unidades-pane" type="button"
                                role="tab">Unidades</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold small text-uppercase py-2.5" id="funcoes-tab"
                                data-bs-toggle="tab" data-bs-target="#funcoes-pane" type="button"
                                role="tab">Funções</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold small text-uppercase py-2.5" id="subfuncoes-tab"
                                data-bs-toggle="tab" data-bs-target="#subfuncoes-pane" type="button"
                                role="tab">Subfunções</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold small text-uppercase py-2.5" id="elementos-tab"
                                data-bs-toggle="tab" data-bs-target="#elementos-pane" type="button"
                                role="tab">Elementos</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold small text-uppercase py-2.5" id="recursos-tab"
                                data-bs-toggle="tab" data-bs-target="#recursos-pane" type="button"
                                role="tab">Recursos</button>
                        </li>
                    </ul>

                    <div class="tab-content" id="abasIndicadoresContent">
                        {{-- Unidades --}}
                        <div class="tab-pane fade show active" id="unidades-pane" role="tabpanel" tabindex="0">
                            <x-tabela-transparencia titulo="" :colunas="[
                                ['label' => 'Código', 'align' => 'text-start'],
                                ['label' => 'Descrição', 'align' => 'text-start'],
                                ['label' => 'Empenhado Anterior', 'align' => 'text-end'],
                                ['label' => 'Empenhado Exercício', 'align' => 'text-end'],
                                ['label' => 'Pago Anterior', 'align' => 'text-end'],
                                ['label' => 'Pago Exercício', 'align' => 'text-end'],
                            ]">
                                @foreach ($resumoUnidades as $item)
                                    <tr class="align-middle">
                                        <td class="text-start font-monospace small text-dark fw-semibold">
                                            {{ $item->codigo ?? '--' }}</td>
                                        <td class="text-start text-dark text-truncate" style="max-width: 240px;">
                                            {{ $item->descricao ?? '--' }}</td>
                                        <td class="text-end text-secondary">R$
                                            {{ number_format($item->valor_empenhado_anterior ?? 0, 2, ',', '.') }}</td>
                                        <td class="text-end text-dark fw-semibold">R$
                                            {{ number_format($item->valor_empenhado_exercicio ?? 0, 2, ',', '.') }}</td>
                                        <td class="text-end text-secondary">R$
                                            {{ number_format($item->valor_pago_anterior ?? 0, 2, ',', '.') }}</td>
                                        <td class="text-end text-dark">R$
                                            {{ number_format($item->valor_pago_exercicio ?? 0, 2, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </x-tabela-transparencia>
                        </div>

                        {{-- Funções --}}
                        <div class="tab-pane fade" id="funcoes-pane" role="tabpanel" tabindex="0">
                            <x-tabela-transparencia titulo="" :colunas="[
                                ['label' => 'Código', 'align' => 'text-start'],
                                ['label' => 'Descrição', 'align' => 'text-start'],
                                ['label' => 'Emissão Anterior', 'align' => 'text-end'],
                                ['label' => 'Emissão Exercício', 'align' => 'text-end'],
                                ['label' => 'Pago Anterior', 'align' => 'text-end'],
                                ['label' => 'Pago Exercício', 'align' => 'text-end'],
                            ]">
                                @foreach ($resumoFuncoes as $item)
                                    <tr class="align-middle">
                                        <td class="text-start font-monospace small text-dark fw-semibold">
                                            {{ $item->codigo ?? '--' }}</td>
                                        <td class="text-start text-dark text-truncate" style="max-width: 240px;">
                                            {{ $item->descricao ?? '--' }}</td>
                                        <td class="text-end text-secondary">R$
                                            {{ number_format($item->valor_emissao_anterior ?? 0, 2, ',', '.') }}</td>
                                        <td class="text-end text-dark fw-semibold">R$
                                            {{ number_format($item->valor_emissao_exercicio ?? 0, 2, ',', '.') }}</td>
                                        <td class="text-end text-secondary">R$
                                            {{ number_format($item->valor_pago_anterior ?? 0, 2, ',', '.') }}</td>
                                        <td class="text-end text-dark">R$
                                            {{ number_format($item->valor_pago_exercicio ?? 0, 2, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </x-tabela-transparencia>
                        </div>

                        {{-- Subfunções --}}
                        <div class="tab-pane fade" id="subfuncoes-pane" role="tabpanel" tabindex="0">
                            <x-tabela-transparencia titulo="" :colunas="[
                                ['label' => 'Código', 'align' => 'text-start'],
                                ['label' => 'Descrição', 'align' => 'text-start'],
                                ['label' => 'Emissão Anterior', 'align' => 'text-end'],
                                ['label' => 'Emissão Exercício', 'align' => 'text-end'],
                                ['label' => 'Pago Anterior', 'align' => 'text-end'],
                                ['label' => 'Pago Exercício', 'align' => 'text-end'],
                            ]">
                                @foreach ($resumoSubfuncoes as $item)
                                    <tr class="align-middle">
                                        <td class="text-start font-monospace small text-dark fw-semibold">
                                            {{ $item->codigo ?? '--' }}</td>
                                        <td class="text-start text-dark text-truncate" style="max-width: 240px;">
                                            {{ $item->descricao ?? '--' }}</td>
                                        <td class="text-end text-secondary">R$
                                            {{ number_format($item->valor_emissao_anterior ?? 0, 2, ',', '.') }}</td>
                                        <td class="text-end text-dark fw-semibold">R$
                                            {{ number_format($item->valor_emissao_exercicio ?? 0, 2, ',', '.') }}</td>
                                        <td class="text-end text-secondary">R$
                                            {{ number_format($item->valor_pago_anterior ?? 0, 2, ',', '.') }}</td>
                                        <td class="text-end text-dark">R$
                                            {{ number_format($item->valor_pago_exercicio ?? 0, 2, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </x-tabela-transparencia>
                        </div>

                        {{-- Elementos --}}
                        <div class="tab-pane fade" id="elementos-pane" role="tabpanel" tabindex="0">
                            <x-tabela-transparencia titulo="" :colunas="[
                                ['label' => 'Estrutural', 'align' => 'text-start'],
                                ['label' => 'Descrição', 'align' => 'text-start'],
                                ['label' => 'Empenhado Anterior', 'align' => 'text-end'],
                                ['label' => 'Empenhado Exercício', 'align' => 'text-end'],
                                ['label' => 'Pago Anterior', 'align' => 'text-end'],
                                ['label' => 'Pago Exercício', 'align' => 'text-end'],
                            ]">
                                @foreach ($resumoElementos as $item)
                                    <tr class="align-middle">
                                        <td class="text-start font-monospace small text-dark fw-semibold">
                                            {{ $item->estrutural ?? '--' }}</td>
                                        <td class="text-start text-dark text-truncate" style="max-width: 240px;">
                                            {{ $item->descricao ?? '--' }}</td>
                                        <td class="text-end text-secondary">R$
                                            {{ number_format($item->valor_empenhado_anterior ?? 0, 2, ',', '.') }}</td>
                                        <td class="text-end text-dark fw-semibold">R$
                                            {{ number_format($item->valor_empenhado_exercicio ?? 0, 2, ',', '.') }}</td>
                                        <td class="text-end text-secondary">R$
                                            {{ number_format($item->pagamento_anterior ?? 0, 2, ',', '.') }}</td>
                                        <td class="text-end text-dark">R$
                                            {{ number_format($item->pagamento_exercicio ?? 0, 2, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </x-tabela-transparencia>
                        </div>

                        {{-- Recursos --}}
                        <div class="tab-pane fade" id="recursos-pane" role="tabpanel" tabindex="0">
                            <x-tabela-transparencia titulo="" :colunas="[
                                ['label' => 'Código', 'align' => 'text-start'],
                                ['label' => 'Descrição', 'align' => 'text-start'],
                                ['label' => 'Emissão Anterior', 'align' => 'text-end'],
                                ['label' => 'Emissão Exercício', 'align' => 'text-end'],
                                ['label' => 'Pago Anterior', 'align' => 'text-end'],
                                ['label' => 'Pago Exercício', 'align' => 'text-end'],
                            ]">
                                @foreach ($resumoRecursos as $item)
                                    <tr class="align-middle">
                                        <td class="text-start font-monospace small text-dark fw-semibold">
                                            {{ $item->codigo ?? '--' }}</td>
                                        <td class="text-start text-dark text-truncate" style="max-width: 240px;">
                                            {{ $item->descricao ?? '--' }}</td>
                                        <td class="text-end text-secondary">R$
                                            {{ number_format($item->valor_emissao_anterior ?? 0, 2, ',', '.') }}</td>
                                        <td class="text-end text-dark fw-semibold">R$
                                            {{ number_format($item->valor_emissao_exercicio ?? 0, 2, ',', '.') }}</td>
                                        <td class="text-end text-secondary">R$
                                            {{ number_format($item->valor_pago_anterior ?? 0, 2, ',', '.') }}</td>
                                        <td class="text-end text-dark">R$
                                            {{ number_format($item->valor_pago_exercicio ?? 0, 2, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </x-tabela-transparencia>
                        </div>
                    </div>
                </div>
            </div>

            {{-- QUADRO CONSOLIDADO --}}
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
        .page-content {
            background: var(--bs-body-bg);
        }

        .table-active-row {
            background-color: rgba(59, 130, 246, 0.03) !important;
        }

        .nav-tabs .nav-link {
            border: none;
            color: #6c757d;
        }

        .nav-tabs .nav-link.active {
            border: none;
            border-bottom: 3px solid var(--bs-primary);
            color: var(--bs-primary) !important;
        }
    </style>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            renderSmartChart('chartUnidades', {!! json_encode($resumoUnidadesMensal) !!}, 'Evolução: Unidades Orçamentárias');
            renderSmartChart('chartFuncoes', {!! json_encode($resumoFuncoesMensal) !!}, 'Evolução: Funções');
            renderSmartChart('chartSubfuncoes', {!! json_encode($resumoSubfuncoesMensal) !!}, 'Evolução: Subfunções');
            renderSmartChart('chartElementos', {!! json_encode($resumoElementosMensal) !!}, 'Evolução: Elementos de Despesa');
            renderSmartChart('chartRecursos', {!! json_encode($resumoRecursosMensal) !!}, 'Evolução: Recursos Vinculados');

            // 1. CHART EVOLUÇÃO MENSAL
            const ctxEvolucao = document.getElementById('chartEvolucaoDespesas');
            if (ctxEvolucao) {
                new Chart(ctxEvolucao, {
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
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: value => 'R$ ' + value.toLocaleString('pt-BR')
                                }
                            }
                        }
                    }
                });
            }

            // 2. CHART BI: ROSCA COMPOSIÇÃO DE FUNÇÕES
            const ctxFuncoes = document.getElementById('chartBIFuncoes');
            if (ctxFuncoes) {
                const dadosFuncoes = {!! json_encode($biFuncoes) !!};
                new Chart(ctxFuncoes, {
                    type: 'doughnut',
                    data: {
                        labels: dadosFuncoes.map(i => i.descricao),
                        datasets: [{
                            data: dadosFuncoes.map(i => i.valor_atualizado_exercicio),
                            backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6',
                                '#ec4899', '#64748b'
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        } // Oculto para não quebrar o layout, o hover mostra o dado
                    }
                });
            }

            // 3. CHART BI: BARRA HORIZONTAL TOP UNIDADES
            const ctxUnidades = document.getElementById('chartBIUnidades');
            if (ctxUnidades) {
                const dadosUnidades = {!! json_encode($topUnidades) !!};
                new Chart(ctxUnidades, {
                    type: 'bar',
                    data: {
                        labels: dadosUnidades.map(i => i.descricao.length > 30 ? i.descricao.substring(0,
                            30) + '...' : i.descricao),
                        datasets: [{
                            label: 'Total Empenhado (R$)',
                            data: dadosUnidades.map(i => i.valor_empenhado_exercicio),
                            backgroundColor: '#3b82f6',
                            borderRadius: 6
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { // O objeto plugins engloba legend e tooltip
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    title: function(context) {
                                        const index = context[0].dataIndex;
                                        return dadosUnidades[index].descricao;
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                ticks: {
                                    callback: value => 'R$ ' + value.toLocaleString('pt-BR', {
                                        notation: 'compact'
                                    })
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>

    <script>
        /**
         * Função que gera um gráfico de linha comparativo inteligente
         */
        function renderSmartChart(canvasId, dados, titulo) {
            const ctx = document.getElementById(canvasId);
            if (!ctx) return;

            // 1. Processamento dos dados (CORREÇÃO AQUI)
            const meses = [...new Set(dados.map(i => i.mes))].sort((a, b) => a - b);

            // Função que soma todos os elementos para um mês específico
            const getSomaMes = (mes, campo) => {
                return dados
                    .filter(item => item.mes == mes)
                    .reduce((soma, item) => soma + parseFloat(item[campo] || 0), 0);
            };

            // Define os campos corretamente conforme seu banco
            const campoAtual = 'valor_emissao_exercicio';
            const campoAnt = 'valor_emissao_anterior';

            // Agora as séries pegam a soma de TODOS os elementos daquele mês
            const serieAtual = meses.map(m => getSomaMes(m, campoAtual));
            const serieAnt = meses.map(m => getSomaMes(m, campoAnt));


            // 2. Renderização
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: meses.map(m => m + '/{{ $exercicio }}'),
                    datasets: [{
                            label: '{{ $exercicio }}',
                            data: serieAtual,
                            borderColor: '#2563eb',
                            backgroundColor: 'rgba(37, 99, 235, 0.1)',
                            fill: true,
                            tension: 0.3
                        },
                        {
                            label: '{{ $exercicio - 1 }}',
                            data: serieAnt,
                            borderColor: '#94a3b8',
                            borderDash: [5, 5],
                            tension: 0.3
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: titulo,
                            font: {
                                size: 16
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const val = context.raw;
                                    const index = context.dataIndex;
                                    const ant = context.datasetIndex === 0 ? serieAnt[index] : null;
                                    let label = context.dataset.label + ': R$ ' + val.toLocaleString('pt-BR');
                                    if (ant && ant > 0) {
                                        const diff = val - ant;
                                        const pct = (diff / ant) * 100;
                                        label += ` (${diff > 0 ? '▲' : '▼'} ${pct.toFixed(1)}%)`;
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            ticks: {
                                callback: v => 'R$ ' + v.toLocaleString()
                            }
                        }
                    }
                }
            });
        }
    </script>
@endpush
