<footer class="bg-white pt-5 mt-5 border-top">
    <div class="container">
        <div class="row gy-5 mb-5">
            <div class="col-lg-4">
                <h6 class="fw-bold text-primary text-uppercase mb-3" style="letter-spacing: 1px;">Institucional</h6>
                <p class="text-dark fw-bold mb-1">Prefeitura Municipal de Cacequi</p>
                <p class="text-muted small mb-4">CNPJ: 88.604.897/0001-03</p>

                <div class="d-flex gap-2">
                    <a href="#" class="social-icon" title="Facebook"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="social-icon" title="Instagram"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="social-icon" title="WhatsApp"><i class="bi bi-whatsapp"></i></a>
                </div>
            </div>

            <div class="col-lg-4">
                <h6 class="fw-bold text-dark text-uppercase mb-3" style="letter-spacing: 1px;">Contato</h6>
                <ul class="list-unstyled small text-muted">
                    <li class="mb-3 d-flex align-items-start">
                        <i class="bi bi-geo-alt text-primary me-3 mt-1"></i>
                        <span>Rua Bento Gonçalves, nº 363, Centro<br>Cacequi - RS, 97450-000</span>
                    </li>
                    <li class="mb-3">
                        <i class="bi bi-telephone text-primary me-3"></i>(55) 3254-1311
                    </li>
                    <li>
                        <i class="bi bi-envelope text-primary me-3"></i>pmcacequi@hotmail.com
                    </li>
                </ul>
            </div>

            <div class="col-lg-4 text-lg-end">
                <h6 class="fw-bold text-dark text-uppercase mb-3" style="letter-spacing: 1px;">Transparência</h6>
                <div class="mb-4">
                    <a href="https://www.cacequi.rs.gov.br" target="_blank"
                        class="btn btn-outline-primary btn-sm px-4 rounded-pill">
                        Site Oficial <i class="bi bi-arrow-up-right ms-1"></i>
                    </a>
                </div>
                <img src="{{ asset('img/selo-transparencia.png') }}" alt="Selo Transparência" class="footer-seal">
            </div>
        </div>
    </div>

    <div class="py-4 bg-light border-top">
        <div class="container">
            <div class="row align-items-center opacity-75" style="font-size: 0.85rem;">
                <div class="col-md-6 text-center text-md-start mb-2 mb-md-0">
                    &copy; 2026 <strong>Hardsoft Informática</strong>. Todos os direitos reservados.
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <span class="me-3"><i class="bi bi-shield-check me-1"></i> LGPD e Privacidade</span>
                    <span><i class="bi bi-clock me-1"></i> Seg - Sex: 09h às 15h</span>
                </div>
            </div>
        </div>
    </div>

    <div id="cookie-banner" class="cookie-banner shadow-lg d-none border-top animate__animated animate__fadeInUp">
        <div class="container py-3">
            <div class="row align-items-center">
                <div class="col-md-9 col-12 mb-3 mb-md-0">
                    <div class="d-flex align-items-start gap-3">
                        <div class="cookie-icon d-none d-md-block text-primary">
                            <i class="fas fa-cookie-bite fs-2"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">Aviso de Privacidade e Cookies</h6>
                            <p class="small text-muted mb-0">
                                Este Portal utiliza cookies para melhorar sua experiência de navegação e garantir a
                                segurança dos dados, conforme a
                                <strong>Lei Geral de Proteção de Dados (LGPD)</strong>. Ao continuar navegando, você
                                concorda com nossas
                                <a href="/politica-de-privacidade"
                                    class="text-primary fw-medium text-decoration-none">Políticas de Privacidade</a>.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-12 d-flex justify-content-md-end gap-2">
                    <button id="btn-accept-cookies" class="btn btn-primary btn-sm px-4 fw-bold shadow-sm">
                        ACEITAR
                    </button>
                </div>
            </div>
        </div>
    </div>
</footer>
