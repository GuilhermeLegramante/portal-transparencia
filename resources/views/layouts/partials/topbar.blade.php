<div class="top-bar py-1 border-bottom">
    <div class="container d-flex justify-content-between align-items-center flex-wrap">
        <div class="contact-info small text-muted">
            <i class="fas fa-envelope me-1"></i> pmcacequi@hotmail.com
        </div>

        <div class="d-flex align-items-center flex-wrap gap-2 py-2">
            <a href="https://transp.hardsoftsfa.com/{{ config('app.client_name') }}/covid19/{{ date('Y') }}"
                target="_blank"
                class="btn btn-outline-danger btn-sm rounded-pill px-3 shadow-sm d-flex align-items-center fw-bold transition-all btn-covid">
                <i class="fas fa-virus-slash me-2"></i> Execução Covid-19
            </a>

            <a href="https://portal.tce.rs.gov.br/aplicprod/f?p=50500:16:::NO:RIR:P16_PAG_RETORNO,P16_CD_ORGAO:4,42900&cs=1dRz3nnLjpWNSvstKlfJXmjTyU9Y"
                target="_blank"
                class="btn btn-outline-danger btn-sm rounded-pill px-3 shadow-sm d-flex align-items-center fw-bold transition-all btn-covid">
                <i class="fas fa-file-contract me-2"></i> Contratações Covid-19
            </a>

            <div class="vr mx-2 d-none d-md-block text-secondary opacity-25"></div>

            {{-- <a href="#" class="btn btn-login btn-sm rounded-pill px-3 border fw-medium d-flex align-items-center">
                <i class="fas fa-user-circle me-2"></i> Logar
            </a> --}}
        </div>
    </div>
</div>
