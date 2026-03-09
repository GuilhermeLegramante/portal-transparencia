@props(['titulo', 'cor' => 'primary', 'icone' => 'table', 'colunas'])

@php
    // Gera um ID único baseado no título para não conflitar com outras tabelas na mesma página
    $collapseId = 'collapse-' . Str::slug($titulo);
@endphp

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-{{ $cor }} text-white d-flex justify-content-between align-items-center"
        role="button" data-bs-toggle="collapse" href="#{{ $collapseId }}" aria-expanded="true">

        <span><i class="fa fa-{{ $icone }} me-2"></i> {{ $titulo }}</span>
        <i class="fa fa-chevron-down small"></i>
    </div>

    <div class="collapse show" id="{{ $collapseId }}">
        <div class="table-responsive">
            <table class="table table-hover table-bordered mb-0">
                <thead class="bg-light text-secondary">
                    <tr>
                        @foreach ($colunas as $coluna)
                            <th class="{{ $coluna['align'] ?? 'text-start' }}">
                                <i class="fa fa-{{ $coluna['icone'] ?? 'info-circle' }} me-1"></i>
                                {{ $coluna['label'] }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    {{ $slot }}
                </tbody>
            </table>
        </div>
    </div>
</div>
