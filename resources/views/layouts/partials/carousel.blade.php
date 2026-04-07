<div id="portalCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
    <div class="carousel-indicators">
        <button type="button" data-bs-target="#portalCarousel" data-bs-slide-to="0" class="active"
            aria-current="true"></button>
        <button type="button" data-bs-target="#portalCarousel" data-bs-slide-to="1"></button>
    </div>

    <div class="carousel-inner">
        <div class="carousel-item active bg-gradient-slide1">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-7 animate__animated animate__fadeInLeft">
                        <span class="badge bg-primary mb-3 py-2 px-3 text-uppercase shadow-sm">Portal da
                            Transparência</span>
                        <h2 class="display-5 fw-bold mb-3" style="color: var(--text-main);">Caro Cidadão</h2>
                        <p class="lead text-secondary mb-4">
                            Acompanhe a aplicação dos recursos públicos de <strong>Cacequi</strong> de forma clara,
                            rápida e sem burocracia.
                        </p>
                        <div class="d-flex gap-3">
                            <a href="#saiba-mais" class="btn btn-primary btn-lg px-4 shadow-sm border-0">Saiba Mais</a>
                            <a href="{{ route('ajuda.faq') }}"
                                class="btn btn-outline-secondary btn-lg px-4 bg-white">Dúvidas?</a>
                        </div>
                    </div>
                    <div class="col-md-5 text-center animate__animated animate__zoomIn d-none d-md-block">
                        <img src="{{ asset('img/brasil-lupa.png') }}" class="img-fluid drop-shadow"
                            style="max-height: 350px;">
                    </div>
                </div>
            </div>
        </div>

        <div class="carousel-item bg-gradient-slide2">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-7 animate__animated animate__fadeInUp">
                        <h2 class="display-5 fw-bold mb-4" style="color: var(--text-main);">Gastos e Receitas</h2>
                        <div class="row g-4">
                            <div class="col-sm-6">
                                <div class="card border-0 bg-white shadow-sm rounded-4 p-3">
                                    <div class="card-body p-0">
                                        <h5 class="fw-bold text-primary mb-2"><i
                                                class="fa-solid fa-arrow-trend-down me-2"></i>Despesas</h5>
                                        <p class="small text-muted mb-0">Contratação de obras, compras de bens e
                                            pagamento de pessoal.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="card border-0 bg-white shadow-sm rounded-4 p-3">
                                    <div class="card-body p-0">
                                        <h5 class="fw-bold text-success mb-2"><i
                                                class="fa-solid fa-arrow-trend-up me-2"></i>Receitas</h5>
                                        <p class="small text-muted mb-0">Arrecadação de impostos e transferências
                                            governamentais.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('planejamento.loa.despesa', ['filtro' => 'elemento']) }}"
                            class="btn btn-primary btn-lg mt-4 px-5 shadow-sm">Consultar Agora</a>
                    </div>
                    <div class="col-md-5 text-center animate__animated animate__fadeInRight d-none d-md-block">
                        <img src="{{ asset('img/lupa-gastos.png') }}" class="img-fluid drop-shadow"
                            style="max-height: 350px;">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <button class="carousel-control-prev" type="button" data-bs-target="#portalCarousel" data-bs-slide="prev">
        <span class="carousel-control-icon"><i class="fa-solid fa-chevron-left"></i></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#portalCarousel" data-bs-slide="next">
        <span class="carousel-control-icon"><i class="fa-solid fa-chevron-right"></i></span>
    </button>
</div>
