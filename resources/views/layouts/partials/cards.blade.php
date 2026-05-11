<div class="container mt-5 mb-5">
    <h3 class="fw-light text-secondary mb-4">Resumo do exercício {{ date('Y') }}</h3>

    <div class="row g-4">
        <div class="col-md-3 animate__animated animate__fadeInUp">
            <div class="card summary-card border-0 shadow-sm h-100">
                <div class="d-flex align-items-center mb-3">
                    <div class="icon-shape bg-soft-green text-success me-3">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>
                    <p class="text-muted small mb-0">Gasto previsto atualizado</p>
                </div>
                <h2 class="fw-bold mb-0">R$ {{ number_format($dados['gasto_previsto'], 2, ',', '.') }}</h2>
            </div>
        </div>

        <div class="col-md-3 animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">
            <div class="card summary-card border-0 shadow-sm h-100">
                <div class="d-flex align-items-center mb-3">
                    <div class="icon-shape bg-soft-red text-danger me-3">
                        <i class="fa-solid fa-file-invoice-dollar"></i>
                    </div>
                    <p class="text-muted small mb-0">Gasto empenhado efetuado</p>
                </div>
                <h2 class="fw-bold mb-0">R$ {{ number_format($dados['gasto_executado'], 2, ',', '.') }}</h2>
            </div>
        </div>

        <div class="col-md-3 animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
            <div class="card summary-card border-0 shadow-sm h-100">
                <div class="d-flex align-items-center mb-3">
                    <div class="icon-shape bg-soft-orange text-warning me-3">
                        <i class="fa-solid fa-percentage"></i>
                    </div>
                    <p class="text-muted small mb-0">Comprometido verba</p>
                </div>
                <h2 class="fw-bold mb-0 text-warning">{{ number_format($dados['perc_comprometido'], 2, ',', '.') }} %
                </h2>
            </div>
        </div>

        <div class="col-md-3 animate__animated animate__fadeInUp" style="animation-delay: 0.4s;">
            <div class="card summary-card border-0 shadow-sm h-100">
                <div class="d-flex align-items-center mb-3">
                    <div class="icon-shape bg-soft-green text-success me-3">
                        <i class="fa-solid fa-hand-holding-dollar"></i>
                    </div>
                    <p class="text-muted small mb-0">Arrecadação prevista</p>
                </div>
                <h2 class="fw-bold mb-0">R$ {{ number_format($dados['arrecadacao_prev'], 2, ',', '.') }}</h2>
            </div>
        </div>

        <div class="col-md-3 animate__animated animate__fadeInUp" style="animation-delay: 0.5s;">
            <div class="card summary-card border-0 shadow-sm h-100">
                <div class="d-flex align-items-center mb-3">
                    <div class="icon-shape bg-soft-red text-danger me-3">
                        <i class="fa-solid fa-vault"></i>
                    </div>
                    <p class="text-muted small mb-0">Arrecadação realizada</p>
                </div>
                <h2 class="fw-bold mb-0">R$ {{ number_format($dados['arrecadacao_real'], 2, ',', '.') }}</h2>
            </div>
        </div>

        <div class="col-md-3 animate__animated animate__fadeInUp" style="animation-delay: 0.6s;">
            <div class="card summary-card border-0 shadow-sm h-100">
                <div class="d-flex align-items-center mb-3">
                    <div class="icon-shape bg-soft-orange text-warning me-3">
                        <i class="fa-solid fa-sack-dollar"></i>
                    </div>
                    <p class="text-muted small mb-0">Realizado receita</p>
                </div>
                <h2 class="fw-bold mb-0 text-warning">{{ number_format($dados['perc_receita'], 2, ',', '.') }} %</h2>
            </div>
        </div>

        <div class="col-md-3 animate__animated animate__fadeInUp" style="animation-delay: 0.7s;">
            <div class="card summary-card border-0 shadow-sm h-100">
                <div class="d-flex align-items-center mb-3">
                    <div class="icon-shape bg-soft-blue text-primary me-3">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <p class="text-muted small mb-0">Déficit/Superávit</p>
                </div>
                <h2 class="fw-bold mb-0 {{ $dados['deficit_superavit'] < 0 ? 'text-danger' : 'text-success' }}">
                    R$ {{ number_format($dados['deficit_superavit'], 2, ',', '.') }}
                </h2>
            </div>
        </div>
    </div>
</div>
