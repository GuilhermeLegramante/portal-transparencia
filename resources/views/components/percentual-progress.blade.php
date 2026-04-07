@props(['valor' => 0])

@php
    $percentual = (float) $valor;

    // Lógica de cores padronizada
    if ($percentual < 40) {
        $classeCor = 'bg-danger';
        $textoCor = 'text-danger';
    } elseif ($percentual < 75) {
        $classeCor = 'bg-warning';
        $textoCor = 'text-warning';
    } elseif ($percentual < 100) {
        $classeCor = 'bg-info';
        $textoCor = 'text-info';
    } else {
        $classeCor = 'bg-success';
        $textoCor = 'text-success';
    }
@endphp

<div class="d-flex align-items-center justify-content-center flex-column" style="min-width: 120px;">
    <div class="progress w-100" style="height: 8px; background-color: #e9ecef; border-radius: 10px;">
        <div class="progress-bar progress-bar-striped progress-bar-animated {{ $classeCor }}" role="progressbar"
            style="width: {{ min($percentual, 100) }}%; border-radius: 10px;" aria-valuenow="{{ $percentual }}"
            aria-valuemin="0" aria-valuemax="100">
        </div>
    </div>
    <small class="mt-1 fw-bold {{ $textoCor }}">
        {{ number_format($percentual, 2, ',', '.') }}%
    </small>
</div>
