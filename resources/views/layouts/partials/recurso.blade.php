<div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; overflow: hidden;">
    <div class="card-body p-0">
        <div class="d-flex card-accent-line align-items-center p-4">
            <div class="flex-shrink-0">
                <div class="glossary-icon-container shadow-sm">
                    <i class="fa fa-layer-group fs-4"></i>
                </div>
            </div>
            <div class="ms-4">
                <span class="glossary-label text-primary fw-bold text-uppercase mb-1">Recurso Vinculado</span>
                <h4 class="mb-0 text-dark fw-bold" style="font-family: 'Inter', sans-serif; letter-spacing: -0.5px;">
                    <span class="text-primary opacity-75">{{ $recurso->codigo }}</span>
                    <span class="mx-2 text-muted-light">•</span>
                    {{ $recurso->descricao }}
                </h4>
            </div>
        </div>
    </div>
</div>
