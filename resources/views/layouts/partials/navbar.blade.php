<nav id="menu" class="navbar navbar-expand-lg bg-white shadow-sm py-3">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">
            @php
                $client = config('app.client_name', 'default');
                $baseUrl = "https://transp.hardsoftsfa.com/{$client}/despesa/";
            @endphp
            <img src="{{ asset('img/' . $client . '.png') }}" alt="Logo" style="height: 50px;">
        </a>

        <div class="collapse navbar-collapse justify-content-center">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}"
                        href="{{ route('home') }}">Início</a>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->is('planejamento*') ? 'active fw-bold' : '' }}"
                        href="#" id="navbarPlanejamento" role="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        Planejamento
                    </a>
                    <ul class="dropdown-menu shadow border-0" aria-labelledby="navbarPlanejamento">
                        <li class="dropdown-submenu">
                            <a class="dropdown-item d-flex justify-content-between align-items-center" href="#">
                                Lei Orçamentária Anual (LOA)
                                <i class="fa fa-chevron-right ms-2" style="font-size: 0.7rem;"></i>
                            </a>
                            <ul class="dropdown-menu shadow border-0">
                                <li class="dropdown-submenu">
                                    <a class="dropdown-item d-flex justify-content-between align-items-center"
                                        href="#">
                                        Despesa
                                        <i class="fa fa-chevron-right ms-2" style="font-size: 0.7rem;"></i>
                                    </a>
                                    <ul class="dropdown-menu shadow border-0">
                                        <li><a class="dropdown-item"
                                                href="{{ route('planejamento.loa.despesa', ['filtro' => 'elemento']) }}">Por
                                                Elemento</a>
                                        </li>
                                        <li><a class="dropdown-item"
                                                href="{{ route('planejamento.loa.despesa', ['filtro' => 'orgao']) }}">Por
                                                Órgão</a></li>
                                        <li><a class="dropdown-item"
                                                href="{{ route('planejamento.loa.despesa', ['filtro' => 'recurso']) }}">Por
                                                Recurso</a></li>
                                    </ul>
                                </li>
                                <li class="dropdown-submenu">
                                    <a class="dropdown-item d-flex justify-content-between align-items-center"
                                        href="#">
                                        Receita
                                        <i class="fa fa-chevron-right ms-2" style="font-size: 0.7rem;"></i>
                                    </a>
                                    <ul class="dropdown-menu shadow border-0">
                                        <li><a class="dropdown-item"
                                                href="{{ route('planejamento.loa.receita', ['filtro' => 'elemento']) }}">Por
                                                Elemento</a>
                                        </li>
                                        <li><a class="dropdown-item"
                                                href="{{ route('planejamento.loa.receita', ['filtro' => 'recurso']) }}">Por
                                                Recurso</a></li>
                                        <li><a class="dropdown-item" href="{{ $baseUrl }}duodecimo">Duodécimo</a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->is('despesa*') ? 'active fw-bold' : '' }}"
                        href="#" id="navbarDespesa" role="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        Despesa
                    </a>
                    <ul class="dropdown-menu shadow border-0" aria-labelledby="navbarDespesa">
                        <li><a class="dropdown-item" href="{{ route('despesa.diarias.resumo') }}">Diárias</a></li>
                        <li><a class="dropdown-item" href="{{ $baseUrl }}decreto">Decreto</a></li>
                        <li><a class="dropdown-item" href="{{ $baseUrl }}repasse">Repasse</a></li>
                        <li><a class="dropdown-item" href="{{ $baseUrl }}duodecimo">Duodécimo</a></li>

                        <li class="dropdown-submenu">
                            <a class="dropdown-item d-flex justify-content-between align-items-center" href="#">
                                Empenho Orçamentário
                                <i class="fa fa-chevron-right ms-2" style="font-size: 0.7rem;"></i>
                            </a>
                            <ul class="dropdown-menu shadow border-0">
                                <li>
                                    <a class="dropdown-item" href="{{ route('empenho.credor.index') }}">
                                        Por Credor
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('empenho.elemento.index') }}">
                                        Por Elemento
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('empenho.orgao.index') }}">
                                        Por Órgão
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('empenho.recurso.index') }}">
                                        Por Recurso
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="dropdown-submenu">
                            <a class="dropdown-item d-flex justify-content-between align-items-center" href="#">
                                Execução Orçamentária
                                <i class="fa fa-chevron-right ms-2" style="font-size: 0.7rem;"></i>
                            </a>
                            <ul class="dropdown-menu shadow border-0">
                                <li>
                                    <a class="dropdown-item" href="{{ route('execucao.elemento.index') }}">
                                        Por Elemento
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('execucao.orgao.index') }}">
                                        Por Órgão
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('execucao.recurso.index') }}">
                                        Por Recurso
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('execucao.localizador.index') }}">
                                        Por Localizador
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>

                @if (config('app.client_name') === 'cacequipm' || config('app.client_name') === 'sisprem')
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->is('receita*') ? 'active fw-bold' : '' }}"
                            href="#" id="navbarReceita" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            Receita
                        </a>
                        <ul class="dropdown-menu shadow border-0" aria-labelledby="navbarReceita">

                            {{-- ARRECADAÇÃO --}}
                            <li class="dropdown-submenu">
                                <a class="dropdown-item d-flex justify-content-between align-items-center"
                                    href="#">
                                    Arrecadação <i class="fa fa-chevron-right ms-2" style="font-size: 0.7rem;"></i>
                                </a>
                                <ul class="dropdown-menu shadow border-0">
                                    <li>
                                        <a class="dropdown-item"
                                            href="{{ route('receita.arrecadacao.elemento.index') }}">
                                            Por Elemento
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item"
                                            href="{{ route('receita.arrecadacao.recurso.index') }}">
                                            Por Recurso
                                        </a>
                                    </li>
                                </ul>
                            </li>

                            {{-- EXECUÇÃO ORÇAMENTÁRIA --}}
                            <li class="dropdown-submenu">
                                <a class="dropdown-item d-flex justify-content-between align-items-center"
                                    href="#">
                                    Execução Orçamentária <i class="fa fa-chevron-right ms-2"
                                        style="font-size: 0.7rem;"></i>
                                </a>
                                <ul class="dropdown-menu shadow border-0">
                                    <li>
                                        <a class="dropdown-item"
                                            href="{{ route('receita.execucao.elemento.index') }}">
                                            Por Elemento
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item"
                                            href="{{ route('receita.execucao.recurso.index') }}">
                                            Por Recurso
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                @endif
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->is('compras*') ? 'active fw-bold' : '' }}"
                        href="#" id="navbarCompras" role="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        Compras
                    </a>
                    <ul class="dropdown-menu shadow border-0" aria-labelledby="navbarCompras">
                        <li class="dropdown-submenu">
                            <a class="dropdown-item d-flex justify-content-between align-items-center" href="#">
                                Licitações
                                <i class="fa fa-chevron-right ms-2" style="font-size: 0.7rem;"></i>
                            </a>
                            <ul class="dropdown-menu shadow border-0">
                                <li>
                                    <a class="dropdown-item" href="{{ route('compras.licitacoes.processo.index') }}">
                                        Processo Licitatório
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('compras.licitacoes.contrato.index') }}">
                                        Contrato Administrativo
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li><a class="dropdown-item" href="{{ route('compras.registro-preco.index') }}">Registro de
                                Preços</a></li>

                        <li class="dropdown-submenu">
                            <a class="dropdown-item d-flex justify-content-between align-items-center" href="#">
                                Requisição de Compras
                                <i class="fa fa-chevron-right ms-2" style="font-size: 0.7rem;"></i>
                            </a>
                            <ul class="dropdown-menu shadow border-0">
                                {{-- Rota: Requisição por Fornecedor --}}
                                <li>
                                    <a class="dropdown-item"
                                        href="{{ route('compras.requisicao.fornecedor.index') }}">
                                        Por Fornecedor
                                    </a>
                                </li>

                                {{-- Rota: Requisição por Solicitante (Secretarias) --}}
                                <li>
                                    <a class="dropdown-item"
                                        href="{{ route('compras.requisicao.solicitante.index') }}">
                                        Por Solicitante
                                    </a>
                                </li>

                                {{-- Rota: Requisição por Elemento de Despesa --}}
                                <li>
                                    <a class="dropdown-item" href="{{ route('compras.requisicao.elemento.index') }}">
                                        Por Elemento
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->is('pessoal*') ? 'active fw-bold' : '' }}"
                        href="#" id="navbarPessoal" role="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        Pessoal
                    </a>
                    <ul class="dropdown-menu shadow border-0" aria-labelledby="navbarPessoal">
                        <li class="dropdown-submenu">
                            <a class="dropdown-item d-flex justify-content-between align-items-center" href="#">
                                Quadro Funcional
                                <i class="fa fa-chevron-right small"></i>
                            </a>
                            <ul class="dropdown-menu shadow border-0">
                                {{-- Rota: Quadro por Função --}}
                                <li>
                                    <a class="dropdown-item" href="{{ route('pessoal.quadro.funcao') }}">
                                        Por Função
                                    </a>
                                </li>

                                {{-- Rota: Quadro por Lotação --}}
                                <li>
                                    <a class="dropdown-item" href="{{ route('pessoal.quadro.lotacao') }}">
                                        Por Lotação
                                    </a>
                                </li>

                                {{-- Rota: Quadro por Regime --}}
                                <li>
                                    <a class="dropdown-item" href="{{ route('pessoal.quadro.regime') }}">
                                        Por Regime
                                    </a>
                                </li>

                                {{-- Rota: Relação Nominal / Por Servidor --}}
                                <li>
                                    <a class="dropdown-item" href="{{ route('pessoal.quadro.servidor') }}">
                                        Por Servidor
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="dropdown-submenu">
                            <a class="dropdown-item d-flex justify-content-between align-items-center" href="#">
                                Folha de Pagamento
                                <i class="fa fa-chevron-right ms-2" style="font-size: 0.7rem;"></i>
                            </a>
                            <ul class="dropdown-menu shadow border-0">
                                <li>
                                    <a class="dropdown-item" href="{{ route('pessoal.folha.funcao') }}">
                                        Por Função
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('pessoal.folha.lotacao') }}">
                                        Por Lotação
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('pessoal.folha.regime') }}">
                                        Por Regime
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('pessoal.folha.servidor') }}">
                                        Por Servidor
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('financas.cronograma') ? 'active fw-bold' : '' }}"
                        href="{{ route('financas.cronograma') }}">
                        Cronograma
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('patrimonio*') ? 'active fw-bold' : '' }}"
                        href="{{ route('patrimonio.index') }}">
                        Patrimônio
                    </a>
                </li>
                {{-- AJUSTADO: Menu Dinâmico de Publicações (Listagem + Cadastro) --}}
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->is('publicacoes*') ? 'active fw-bold' : '' }}"
                        href="#" id="navbarPublicacoes" role="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        Publicações
                    </a>
                    <ul class="dropdown-menu shadow border-0" aria-labelledby="navbarPublicacoes">
                        <li>
                            <a class="dropdown-item {{ !request()->has('categoria') && request()->is('publicacoes') ? 'active' : '' }}"
                                href="{{ route('publicacoes.index') }}">
                                <i class="fas fa-list me-2 opacity-75"></i>Consultar Publicações
                            </a>
                        </li>

                        {{-- SEÇÃO: PRESTAÇÃO DE CONTAS --}}
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li class="dropdown-header text-uppercase small fw-bold text-muted px-3 pt-1 pb-2">
                            <i class="fas fa-file-invoice-dollar me-1"></i> Prestação de Contas
                        </li>
                        <li>
                            <a class="dropdown-item {{ request()->get('categoria') === 'BALANÇO' ? 'active fw-bold' : '' }}"
                                href="{{ route('publicacoes.index', ['categoria' => 'BALANÇO']) }}">
                                <i class="fas fa-balance-scale me-2 opacity-75 text-secondary"></i>Balanço
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item {{ request()->get('categoria') === 'DEMONSTRATIVO' ? 'active fw-bold' : '' }}"
                                href="{{ route('publicacoes.index', ['categoria' => 'DEMONSTRATIVO']) }}">
                                <i class="fas fa-chart-line me-2 opacity-75 text-secondary"></i>Demonstrativo
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item {{ request()->get('categoria') === 'RELATÓRIO' ? 'active fw-bold' : '' }}"
                                href="{{ route('publicacoes.index', ['categoria' => 'RELATÓRIO']) }}">
                                <i class="fas fa-file-alt me-2 opacity-75 text-secondary"></i>Relatório - RGF
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item {{ request()->get('categoria') === 'RELATÓRIO CIRCUNSTANCIADO' ? 'active fw-bold' : '' }}"
                                href="{{ route('publicacoes.index', ['categoria' => 'RELATÓRIO CIRCUNSTANCIADO']) }}">
                                <i class="fas fa-file-signature me-2 opacity-75 text-secondary"></i>Relatório
                                Circunstanciado
                            </a>
                        </li>

                        {{-- ÁREA ADMINISTRATIVA (Apenas Logados) --}}
                        @auth
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a class="dropdown-item fw-bold text-success" href="{{ route('publicacoes.create') }}">
                                    <i class="fas fa-plus-circle me-2"></i>Nova Publicação
                                </a>
                            </li>
                        @endauth
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->is('parlamentar*') ? 'active fw-bold' : '' }}"
                        href="#" id="navbarParlamentar" role="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        Parlamentar
                    </a>
                    <ul class="dropdown-menu shadow border-0" aria-labelledby="navbarParlamentar">

                        {{-- Submenu: Legislatura (Parlamentares e Mesa) --}}
                        <li class="dropdown-submenu">
                            <a class="dropdown-item d-flex justify-content-between align-items-center" href="#">
                                Legislatura
                                <i class="fa fa-chevron-right ms-2" style="font-size: 0.7rem;"></i>
                            </a>
                            <ul class="dropdown-menu shadow border-0">
                                <li>
                                    <a class="dropdown-item" href="{{ route('parlamentar.index') }}">
                                        Parlamentares
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item"
                                        href="{{ route('parlamentar.index', ['tab' => 'mesa']) }}">
                                        Mesa Diretora
                                    </a>
                                </li>
                            </ul>
                        </li>

                        {{-- Item Direto: Sessões --}}
                        <li>
                            <a class="dropdown-item" href="{{ route('parlamentar.sessao.index') }}">
                                Sessões
                            </a>
                        </li>

                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->is('ajuda*') ? 'active fw-bold' : '' }}"
                        href="#" id="navbarAjuda" role="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        Ajuda
                    </a>
                    <ul class="dropdown-menu shadow border-0" aria-labelledby="navbarAjuda">
                        {{-- <li><a class="dropdown-item" href="#">Glossário</a></li> --}}
                        <li><a class="dropdown-item" href="{{ route('ajuda.faq') }}">Perguntas Frequentes (FAQ)</a>
                        </li>
                        {{-- <li><a class="dropdown-item" href="#">Links Úteis</a></li> --}}
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->is('sic*') ? 'active fw-bold' : '' }}"
                        href="#" id="navbarSIC" role="button" data-bs-toggle='dropdown'>
                        SIC
                    </a>
                    <ul class="dropdown-menu shadow border-0">
                        <li><a class="dropdown-item" href="{{ route('sic.index') }}">Sobre o SIC / Guia</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item fw-bold text-primary" href="{{ route('sic.login') }}">Solicitar
                                Informação (Login)</a></li>
                        <li><a class="dropdown-item" href="{{ route('sic.estatisticas') }}">Consulta Estatística</a>
                        </li>
                        <li><a class="dropdown-item" href="{{ route('sic.contato') }}">Fale Conosco</a></li>
                    </ul>
                </li>
            </ul>

            {{-- NOVO ELEMENTO: Área de Login / Controle de Sessão à Direita --}}
            <div class="d-flex align-items-center mt-3 mt-lg-0">
                @auth
                    {{-- Usuário Autenticado - Dropdown Centralizado Unificado --}}
                    <div class="dropdown">
                        <button
                            class="btn btn-outline-secondary dropdown-toggle rounded-3 px-3 d-flex align-items-center gap-2"
                            type="button" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle fa-lg text-secondary"></i>
                            <span class="small fw-bold">{{ Auth::user()->name ?? 'Painel' }}</span>
                        </button>

                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 p-2 rounded-3"
                            aria-labelledby="userMenu" style="min-width: 210px;">
                            {{-- Seção Administrativa Geral --}}
                            <div class="dropdown-header text-uppercase text-muted small fw-bold px-3 pt-1 pb-1"
                                style="font-size: 0.7rem;">Administração</div>
                            <li>
                                <a class="dropdown-item d-flex align-items-center rounded-2 py-2"
                                    href="{{ route('publicacoes.create') }}">
                                    <i class="fas fa-file-upload me-2 text-success opacity-75"></i> Nova Publicação
                                </a>
                            </li>

                            {{-- Seção Executiva / Gestão (Nova) --}}
                            <li>
                                <hr class="dropdown-divider my-2">
                            </li>
                            <div class="dropdown-header text-uppercase text-muted small fw-bold px-3 pb-1"
                                style="font-size: 0.7rem;">Painel do Gestor</div>
                            <li>
                                <a class="dropdown-item d-flex align-items-center rounded-2 py-2 {{ request()->routeIs('gestor.indicadores.*') ? 'active fw-bold' : '' }}"
                                    href="{{ route('gestor.indicadores.index') }}">
                                    <i class="fas fa-chart-bar me-2 text-primary opacity-75"></i> Indicadores Contábeis
                                </a>
                            </li>

                            {{-- Ação de Logout --}}
                            <li>
                                <hr class="dropdown-divider my-2">
                            </li>
                            <li>
                                <a class="dropdown-item text-danger d-flex align-items-center rounded-2 py-2"
                                    href="#"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt me-2 opacity-75"></i> Sair do Painel
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    {{-- Usuário Visitante (Link de Acesso Administrativo) --}}
                    <a href="{{ route('login') }}"
                        class="btn btn-primary rounded-3 px-3 py-1.5 fw-bold shadow-sm d-inline-flex align-items-center gap-1.5 text-nowrap"
                        style="font-size: 0.825rem; height: 36px; line-height: 1;">
                        <i class="fas fa-lock" style="font-size: 0.75rem;"></i>
                        <span>&nbsp;Acesso Restrito</span>
                    </a>
                @endauth
            </div>
        </div>
    </div>
    </div>
</nav>
