<div class="container mt-5 mb-5">
    <h3 class="fw-light text-secondary mb-4">Resumo do exercício {{ date('Y') }}</h3>

    <div class="row g-4">
        <div class="col-md-3">
            <div class="card summary-card border-0 shadow-sm h-100">
                <div class="d-flex align-items-center mb-3">
                    <div class="icon-shape bg-soft-green text-success me-3">
                        <i class="fa-solid fa-arrow-down"></i>
                    </div>
                    <p class="text-muted small mb-0">Gasto previsto atualizado</p>
                </div>
                <h2 class="fw-bold mb-0">{{ number_format($dados['gasto_previsto'], 2, ',', '.') }}</h2>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card summary-card border-0 shadow-sm h-100">
                <div class="d-flex align-items-center mb-3">
                    <div class="icon-shape bg-soft-red text-danger me-3">
                        <i class="fa-solid fa-arrow-down"></i>
                    </div>
                    <p class="text-muted small mb-0">Gasto empenhado efetuado</p>
                </div>
                <h2 class="fw-bold mb-0">{{ number_format($dados['gasto_executado'], 2, ',', '.') }}</h2>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card summary-card border-0 shadow-sm h-100">
                <div class="d-flex align-items-center mb-3">
                    <div class="icon-shape bg-soft-orange text-warning me-3">
                        <i class="fa-solid fa-chart-simple"></i>
                    </div>
                    <p class="text-muted small mb-0">Comprometido verba</p>
                </div>
                <h2 class="fw-bold mb-0 text-warning">{{ number_format($dados['perc_comprometido'], 2, ',', '.') }} %
                </h2>
            </div>
        </div>

        {{-- Só exibe para cacequipm e sisprem --}}
        @if (config('app.client_name') == 'cacequipm' || config('app.client_name') == 'sisprem')
            <div class="col-md-3">
                <div class="card summary-card border-0 shadow-sm h-100">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-shape bg-soft-blue text-primary me-3">
                            <i class="fa-solid fa-info"></i>
                        </div>
                        <p class="text-muted small mb-0">Repasse previsto legislativo</p>
                    </div>
                    <h2 class="fw-bold mb-0 text-primary">{{ number_format($dados['repasse_leg'], 2, ',', '.') }}</h2>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card summary-card border-0 shadow-sm h-100">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-shape bg-soft-green text-success me-3">
                            <i class="fa-solid fa-arrow-up"></i>
                        </div>
                        <p class="text-muted small mb-0">Arrecadação prevista orçamento</p>
                    </div>
                    <h2 class="fw-bold mb-0">{{ number_format($dados['arrecadacao_prev'], 2, ',', '.') }}</h2>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card summary-card border-0 shadow-sm h-100">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-shape bg-soft-red text-danger me-3">
                            <i class="fa-solid fa-arrow-up"></i>
                        </div>
                        <p class="text-muted small mb-0">Arrecadação realizada</p>
                    </div>
                    <h2 class="fw-bold mb-0">{{ number_format($dados['arrecadacao_real'], 2, ',', '.') }}</h2>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card summary-card border-0 shadow-sm h-100">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-shape bg-soft-orange text-warning me-3">
                            <i class="fa-solid fa-chart-simple"></i>
                        </div>
                        <p class="text-muted small mb-0">Realizado receita</p>
                    </div>
                    <h2 class="fw-bold mb-0 text-warning">{{ number_format($dados['perc_receita'], 2, ',', '.') }} %
                    </h2>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card summary-card border-0 shadow-sm h-100">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-shape bg-soft-blue text-primary me-3">
                            <i class="fa-solid fa-arrow-down"></i>
                        </div>
                        <p class="text-muted small mb-0">Déficit orçamentário</p>
                    </div>
                    <h2 class="fw-bold mb-0 text-primary">{{ number_format($dados['deficit_orc'], 2, ',', '.') }}</h2>
                </div>
            </div>
        @endif
    </div>
</div>
