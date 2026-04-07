<div class="card border-0 shadow-sm mb-4">
    <div class="card-body bg-light border-start border-primary border-4">
        <div class="d-flex align-items-center">
            <div class="flex-shrink-0">
                {{-- Ícone de Prédio/Instituição para representar o Órgão --}}
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                    style="width: 50px; height: 50px;">
                    <i class="fa fa-building fa-lg"></i>
                </div>
            </div>
            <div class="ms-3">
                <h6 class="text-muted mb-1 text-uppercase small fw-bold">Unidade Gestora / Órgão</h6>
                <h4 class="mb-0 text-dark">
                    <span class="text-primary">{{ str_pad($orgao->codigo, 2, '0', STR_PAD_LEFT) }}</span> -
                    {{ $orgao->descricao }}
                </h4>
            </div>
        </div>
    </div>
</div>
