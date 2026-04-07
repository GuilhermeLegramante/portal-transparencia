@props(['titulo', 'cor' => 'primary', 'icone' => 'table', 'colunas'])

@php
    $collapseId = 'collapse-' . Str::slug($titulo) . '-' . uniqid();
    // Mapa de cores para o gradiente e acentos modernos
    $colorMap = [
        'primary' => ['bg' => '#f0f7ff', 'text' => '#0056b3', 'border' => '#0056b3'],
        'purple' => ['bg' => '#f5f3ff', 'text' => '#6d28d9', 'border' => '#6d28d9'],
        'warning' => ['bg' => '#fffbeb', 'text' => '#b45309', 'border' => '#f59e0b'],
        'success' => ['bg' => '#f0fdf4', 'text' => '#15803d', 'border' => '#22c55e'],
    ];
    $theme = $colorMap[$cor] ?? $colorMap['primary'];
@endphp

<div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; overflow: hidden;">
    {{-- Header Moderno com Estilo Minimalista --}}
    <div class="card-header border-0 p-4 d-flex justify-content-between align-items-center"
        style="background-color: #ffffff; cursor: pointer;" data-bs-toggle="collapse" href="#{{ $collapseId }}"
        aria-expanded="true">

        <div class="d-flex align-items-center">
            <div class="d-flex align-items-center justify-content-center shadow-sm me-3"
                style="width: 40px; height: 40px; background-color: {{ $theme['bg'] }}; color: {{ $theme['text'] }}; border-radius: 10px;">
                <i class="fa fa-{{ $icone }} fs-5"></i>
            </div>
            <div>
                <h5 class="mb-0 text-dark fw-bold" style="letter-spacing: -0.5px;">{{ $titulo }}</h5>
                <span class="text-muted small">Clique para expandir ou recolher</span>
            </div>
        </div>

        <div class="text-muted bg-light rounded-circle d-flex align-items-center justify-content-center"
            style="width: 28px; height: 28px;">
            <i class="fa fa-chevron-down small"></i>
        </div>
    </div>

    <div class="collapse show" id="{{ $collapseId }}">
        <div class="card-body p-0"> {{-- Removido padding para a tabela ocupar toda a largura --}}
            <div class="table-responsive">
                <table class="table table-hover mb-0 js-datatable"
                    style="width:100%; border-collapse: separate; border-spacing: 0;">
                    <thead style="background-color: #f8fafc;">
                        <tr>
                            @foreach ($colunas as $coluna)
                                <th class="{{ $coluna['align'] ?? 'text-start' }} align-middle border-bottom-0"
                                    style="padding: 15px 20px; color: #64748b; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; white-space: nowrap;">
                                    @if (isset($coluna['icone']) && $coluna['icone'])
                                        <i class="fa fa-{{ $coluna['icone'] }} me-1 opacity-50"></i>
                                    @endif
                                    {{ $coluna['label'] }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody style="border-top: 1px solid #f1f5f9;">
                        {{ $slot }}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    /* Estilo para as linhas da tabela - Design Moderno */
    .table-hover tbody tr {
        transition: background-color 0.2s ease;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.02) !important;
    }

    .table-hover td {
        padding: 16px 20px !important;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
        font-size: 0.875rem;
    }

    /* Remove a borda da última linha */
    .table-hover tbody tr:last-child td {
        border-bottom: 0;
    }

    /* Estilização para DataTables (se estiver usando) */
    .dataTables_wrapper .dataTables_filter {
        padding: 15px 20px;
    }

    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_paginate {
        padding: 15px 20px;
        font-size: 0.8rem;
    }
</style>
