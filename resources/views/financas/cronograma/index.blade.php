@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <x-breadcrumb :items="$breadcrumb" />

        {{-- 1. Resumo por Recurso (Layout em Cards Roláveis ou Grid) --}}
        <div class="row mb-4">
            <div class="col-12">
                <h5 class="fw-bold mb-3"><i class="fa fa-chart-pie me-2 text-primary"></i>Resumo por Recurso Vinculado</h5>
            </div>
            @foreach ($resumoRecursos as $res)
                <div class="col-md-4 col-xl-3 mb-3">
                    <div class="card shadow-sm border-0 border-start border-primary border-4">
                        <div class="card-body p-3">
                            <small class="text-muted fw-bold">RECURSO {{ $res->codigo_recurso }}</small>
                            <p class="small text-truncate mb-2" title="{{ $res->descricao_recurso }}">
                                {{ $res->descricao_recurso }}</p>

                            <div class="d-flex justify-content-between mb-1">
                                <span class="small">Liquidado:</span>
                                <span class="small fw-bold">R$
                                    {{ number_format($res->valor_liquidado, 2, ',', '.') }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="small text-success">Pago:</span>
                                <span class="small fw-bold text-success">R$
                                    {{ number_format($res->valor_pago, 2, ',', '.') }}</span>
                            </div>
                            <hr class="my-1">
                            <div class="d-flex justify-content-between">
                                <span class="small fw-bold">Saldo:</span>
                                <span class="small fw-bold text-danger">R$
                                    {{ number_format($res->saldo_pagar, 2, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- 2. Filtros --}}
        <form class="card p-3 mb-4 shadow-sm border-0 bg-light" method="GET">
            <div class="row g-3">
                <div class="col-md-2">
                    <label class="small fw-bold">Exercício</label>
                    <input type="number" name="exercicio" value="{{ $exercicio }}" class="form-control">
                </div>
                <div class="col-md-7">
                    <label class="small fw-bold">Buscar por Recurso ou Credor</label>
                    <input type="text" name="busca" class="form-control" placeholder="Filtro rápido...">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button class="btn btn-primary w-100"><i class="fa fa-filter me-2"></i>Filtrar Resultados</button>
                </div>
            </div>
        </form>

        {{-- 3. Tabela de Pagamentos --}}
        <x-tabela-transparencia titulo="Relação de Pagamentos (Cronograma)" :colunas="[
            ['label' => 'Liquidação / Vencimento', 'align' => 'text-center'],
            ['label' => 'Credor / Empenho', 'align' => 'text-start'],
            ['label' => 'Recurso / Justificativa', 'align' => 'text-start'],
            ['label' => 'Valor', 'align' => 'text-end'],
            ['label' => 'Situação', 'align' => 'text-center'],
        ]">
            @foreach ($pagamentos as $pg)
                <tr>
                    <td class="text-center">
                        <span class="d-block small">Liq:
                            <strong>{{ date('d/m/Y', strtotime($pg->data_liquidacao)) }}</strong></span>
                        <span class="d-block small text-primary">Venc:
                            <strong>{{ date('d/m/Y', strtotime($pg->data_vencimento)) }}</strong></span>
                    </td>
                    <td>
                        <span class="fw-bold d-block">{{ $pg->nome_credor }}</span>
                        <small class="text-muted">Empenho: {{ $pg->empenho_numero }} | NF:
                            {{ $pg->nota_fiscal ?? 'N/A' }}</small>
                    </td>
                    <td>
                        <small class="badge bg-secondary mb-1">
                            {{ $pg->codigo_recurso }} - {{ $pg->descricao_recurso }}
                        </small>

                        <p class="small text-muted mb-0 text-truncate" style="max-width: 250px; cursor: help;"
                            data-bs-toggle="tooltip" data-bs-html="true" title="{{ $pg->justificativa }}">
                            {{ $pg->justificativa }}
                        </p>
                    </td>
                    <td class="text-end fw-bold">
                        R$ {{ number_format($pg->valor, 2, ',', '.') }}
                    </td>
                    <td class="text-center">
                        @if ($pg->pagamento_realizado)
                            <span class="badge bg-success"><i class="fa fa-check-circle me-1"></i> Realizado</span>
                        @else
                            <span class="badge bg-warning text-dark"><i class="fa fa-clock me-1"></i> Pendente</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </x-tabela-transparencia>

        {{-- <div class="mt-3">
            {{ $pagamentos->appends(request()->all())->links() }}
        </div> --}}
    </div>
@endsection

@push('scripts')
    <script>
        // Ativar tooltips do Bootstrap
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    </script>
@endpush
