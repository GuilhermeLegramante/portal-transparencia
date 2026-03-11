{{-- @props(['titulo', 'cor' => 'primary', 'icone' => 'table', 'colunas'])

@php
    $collapseId = 'collapse-' . Str::slug($titulo) . '-' . uniqid();
@endphp

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-{{ $cor }} text-white d-flex justify-content-between align-items-center"
         role="button" data-bs-toggle="collapse" href="#{{ $collapseId }}" aria-expanded="true">
        <span><i class="fa fa-{{ $icone }} me-2"></i> {{ $titulo }}</span>
        <i class="fa fa-chevron-down small"></i>
    </div>

    <div class="collapse show" id="{{ $collapseId }}">
        <div class="card-body p-3"> {{-- Padding para o campo de busca não colar na borda --}}
@props(['titulo', 'cor' => 'primary', 'icone' => 'table', 'colunas'])

@php
    $collapseId = 'collapse-' . Str::slug($titulo) . '-' . uniqid();
@endphp

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-{{ $cor }} text-white d-flex justify-content-between align-items-center"
        role="button" data-bs-toggle="collapse" href="#{{ $collapseId }}" aria-expanded="true">
        <span><i class="fa fa-{{ $icone }} me-2"></i> {{ $titulo }}</span>
        <i class="fa fa-chevron-down small"></i>
    </div>

    <div class="collapse show" id="{{ $collapseId }}">
        <div class="card-body p-3"> {{-- Padding para o campo de busca não colar na borda --}}
            <div class="table-responsive">
                <table class="table table-hover table-bordered mb-0 js-datatable" style="width:100%">
                    <thead class="bg-light text-secondary">
                        <tr>
                            @foreach ($colunas as $coluna)
                                <th class="{{ $coluna['align'] ?? 'text-start' }} small text-uppercase">
                                    <i class="fa fa-{{ $coluna['icone'] ?? 'sort' }} me-1 opacity-50"></i>
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
</div>
