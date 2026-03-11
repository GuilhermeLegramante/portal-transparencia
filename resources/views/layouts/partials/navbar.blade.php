<nav class="navbar navbar-expand-lg bg-white shadow-sm py-3">
    <div class="container">
        <a class="navbar-brand" href="#">
            @php
                $client = env('CLIENT_NAME', 'default');
            @endphp
            <img src="{{ asset('img/' . $client . '.png') }}" alt="Logo" style="height: 50px;">
        </a>

        <div class="collapse navbar-collapse justify-content-center">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link active" href="#">Início</a></li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarPlanejamento" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        Planejamento
                    </a>
                    <ul class="dropdown-menu shadow border-0" aria-labelledby="navbarPlanejamento">
                        <li class="dropdown-submenu">
                            <a class="dropdown-item d-flex justify-content-between align-items-center" href="#">
                                Lei Orçamentária Anual (LOA)
                            </a>
                            <ul class="dropdown-menu shadow border-0">
                                <li class="dropdown-submenu">
                                    <a class="dropdown-item d-flex justify-content-between align-items-center"
                                        href="#">
                                        Despesa
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
                                    </a>
                                    <ul class="dropdown-menu shadow border-0">
                                        <li><a class="dropdown-item"
                                                href="{{ route('planejamento.loa.receita', ['filtro' => 'elemento']) }}">Por
                                                Elemento</a>
                                        </li>
                                        <li><a class="dropdown-item"
                                                href="{{ route('planejamento.loa.receita', ['filtro' => 'recurso']) }}">Por
                                                Recurso</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDespesa" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        Despesa
                    </a>
                    <ul class="dropdown-menu shadow border-0" aria-labelledby="navbarDespesa">
                        <li><a class="dropdown-item" href="#">Diária</a></li>
                        <li><a class="dropdown-item" href="#">Decreto</a></li>
                        <li><a class="dropdown-item" href="#">Repasse</a></li>
                        <li><a class="dropdown-item" href="#">Duodécimo</a></li>

                        <li class="dropdown-submenu">
                            <a class="dropdown-item d-flex justify-content-between align-items-center" href="#">
                                Empenho Orçamentário
                            </a>
                            <ul class="dropdown-menu shadow border-0">
                                <li><a class="dropdown-item" href="#">Por Credor</a></li>
                                <li><a class="dropdown-item" href="#">Por Elemento</a></li>
                                <li><a class="dropdown-item" href="#">Por Órgão</a></li>
                                <li><a class="dropdown-item" href="#">Por Recurso</a></li>
                            </ul>
                        </li>

                        <li class="dropdown-submenu">
                            <a class="dropdown-item d-flex justify-content-between align-items-center" href="#">
                                Execução Orçamentária
                            </a>
                            <ul class="dropdown-menu shadow border-0">
                                <li><a class="dropdown-item" href="#">Por Elemento</a></li>
                                <li><a class="dropdown-item" href="#">Por Órgão</a></li>
                                <li><a class="dropdown-item" href="#">Por Recurso</a></li>
                                <li><a class="dropdown-item" href="#">Por Localizador</a></li>
                            </ul>
                        </li>
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarReceita" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        Receita
                    </a>
                    <ul class="dropdown-menu shadow border-0" aria-labelledby="navbarReceita">
                        <li class="dropdown-submenu">
                            <a class="dropdown-item d-flex justify-content-between align-items-center" href="#">
                                Arrecadação
                            </a>
                            <ul class="dropdown-menu shadow border-0">
                                <li><a class="dropdown-item" href="#">Por Elemento</a></li>
                                <li><a class="dropdown-item" href="#">Por Recurso</a></li>
                            </ul>
                        </li>

                        <li class="dropdown-submenu">
                            <a class="dropdown-item d-flex justify-content-between align-items-center" href="#">
                                Execução Orçamentária
                            </a>
                            <ul class="dropdown-menu shadow border-0">
                                <li><a class="dropdown-item" href="#">Por Elemento</a></li>
                                <li><a class="dropdown-item" href="#">Por Recurso</a></li>
                            </ul>
                        </li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarCompras" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        Compras
                    </a>
                    <ul class="dropdown-menu shadow border-0" aria-labelledby="navbarCompras">
                        <li class="dropdown-submenu">
                            <a class="dropdown-item d-flex justify-content-between align-items-center" href="#">
                                Licitações
                            </a>
                            <ul class="dropdown-menu shadow border-0">
                                <li><a class="dropdown-item" href="#">Processo Licitatório</a></li>
                                <li><a class="dropdown-item" href="#">Contrato Administrativo</a></li>
                            </ul>
                        </li>

                        <li><a class="dropdown-item" href="#">Registro de Preços</a></li>

                        <li class="dropdown-submenu">
                            <a class="dropdown-item d-flex justify-content-between align-items-center" href="#">
                                Requisição de Compras
                            </a>
                            <ul class="dropdown-menu shadow border-0">
                                <li><a class="dropdown-item" href="#">Por Fornecedor</a></li>
                                <li><a class="dropdown-item" href="#">Por Solicitação</a></li>
                            </ul>
                        </li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarPessoal" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        Pessoal
                    </a>
                    <ul class="dropdown-menu shadow border-0" aria-labelledby="navbarPessoal">
                        <li class="dropdown-submenu">
                            <a class="dropdown-item d-flex justify-content-between align-items-center" href="#">
                                Quadro Funcional
                            </a>
                            <ul class="dropdown-menu shadow border-0">
                                <li><a class="dropdown-item" href="#">Por Função</a></li>
                                <li><a class="dropdown-item" href="#">Por Lotação</a></li>
                                <li><a class="dropdown-item" href="#">Por Regime</a></li>

                            </ul>
                        </li>

                        <li class="dropdown-submenu">
                            <a class="dropdown-item d-flex justify-content-between align-items-center" href="#">
                                Folha de Pagamento
                            </a>
                            <ul class="dropdown-menu shadow border-0">
                                <li><a class="dropdown-item" href="#">Por Função</a></li>
                                <li><a class="dropdown-item" href="#">Por Lotação</a></li>
                                <li><a class="dropdown-item" href="#">Por Regime</a></li>
                                <li><a class="dropdown-item" href="#">Por Servidor</a></li>
                            </ul>
                        </li>
                    </ul>
                </li>
                <li class="nav-item"><a class="nav-link" href="#">Cronograma</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Patrimônio</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarPublicacao" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        Publicação
                    </a>
                    <ul class="dropdown-menu shadow border-0" aria-labelledby="navbarPublicacao">
                        <li><a class="dropdown-item" href="#">Prestação de Contas</a></li>
                        <li><a class="dropdown-item" href="#">Outras Publicações</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarAjuda" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        Ajuda
                    </a>
                    <ul class="dropdown-menu shadow border-0" aria-labelledby="navbarAjuda">
                        <li><a class="dropdown-item" href="#">Glossário</a></li>
                        <li><a class="dropdown-item" href="#">Perguntas Frequentes (FAQ)</a></li>
                        <li><a class="dropdown-item" href="#">Links Úteis</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
