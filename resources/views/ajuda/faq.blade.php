@extends('layouts.app') {{-- Ou o nome do seu layout principal --}}

@section('content')
    <div class="container py-4">
        <x-breadcrumb :items="[
            'Ajuda' => '#',
            'Perguntas frequentes (FAQ)' => '',
        ]" />

        <div class="card border-primary shadow-sm">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center py-3"
                style="">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-question-circle me-2"></i> Perguntas frequentes (FAQ)
                </h5>
                <i class="fas fa-chevron-down"></i>
            </div>

            <div class="card-body p-3">
                <div class="accordion accordion-flush" id="accordionFaq">

                    <div class="accordion-item border mb-2">
                        <h2 class="accordion-header" id="headingOne">
                            <button class="accordion-button bg-light text-secondary fw-medium collapsed" type="button"
                                data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false"
                                aria-controls="collapseOne">
                                Quais os instrumentos normativos (Leis, Decretos) disciplinam a transparência no Brasil?
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne"
                            data-bs-parent="#accordionFaq">
                            <div class="accordion-body text-muted small">
                                <ul class="mb-0">
                                    <li class="mb-2"><strong>Lei Complementar nº 101, de 04 de Maio de 2000:</strong>
                                        Estabelece normas de finanças públicas voltadas para a responsabilidade na gestão
                                        fiscal e dá outras providências.</li>
                                    <li class="mb-2"><strong>Lei Complementar nº 131, de 27 de Maio de 2009:</strong>
                                        Acrescenta dispositivos à Lei Complementar nº 101... a fim de determinar a
                                        disponibilização, em tempo real, de informações pormenorizadas sobre a execução.
                                    </li>
                                    <li><strong>Decreto nº 7.185, de 27 de Maio de 2010:</strong> Dispõe sobre o padrão
                                        mínimo de qualidade do sistema integrado de administração financeira e controle...
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item border mb-2">
                        <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button bg-light text-secondary fw-medium collapsed" type="button"
                                data-bs-toggle="collapse" data-bs-target="#collapseTwo">
                                Por que o Portal da Transparência foi criado?
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#accordionFaq">
                            <div class="accordion-body text-muted small">
                                O Portal foi criado para permitir que o cidadão acompanhe como o dinheiro público está sendo
                                utilizado, aumentando o controle social e a transparência da gestão pública.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item border mb-2">
                        <h2 class="accordion-header" id="headingThree">
                            <button class="accordion-button bg-light text-secondary fw-medium collapsed" type="button"
                                data-bs-toggle="collapse" data-bs-target="#collapseThree">
                                Quais as opções de consulta disponíveis no Portal da Transparência?
                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#accordionFaq">
                            <div class="accordion-body text-muted small">
                                Estão disponíveis consultas sobre receitas, despesas, licitações, contratos, folha de
                                pagamento de servidores, diárias e muito mais.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item border mb-2">
                        <h2 class="accordion-header" id="headingFour">
                            <button class="accordion-button bg-light text-secondary fw-medium collapsed" type="button"
                                data-bs-toggle="collapse" data-bs-target="#collapseFour">
                                Qual é a origem dos dados obtidos nessas consultas?
                            </button>
                        </h2>
                        <div id="collapseFour" class="accordion-collapse collapse" data-bs-parent="#accordionFaq">
                            <div class="accordion-body text-muted small">
                                Os dados são extraídos diretamente dos sistemas de planejamento, contabilidade e finanças da
                                entidade. As informações são atualizadas diariamente para garantir que o cidadão tenha
                                acesso aos atos em tempo real, conforme exigido pela Lei de Responsabilidade Fiscal.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item border mb-2">
                        <h2 class="accordion-header" id="headingFive">
                            <button class="accordion-button bg-light text-secondary fw-medium collapsed" type="button"
                                data-bs-toggle="collapse" data-bs-target="#collapseFive">
                                Quem é obrigado a prestar contas dos recursos públicos?
                            </button>
                        </h2>
                        <div id="collapseFive" class="accordion-collapse collapse" data-bs-parent="#accordionFaq">
                            <div class="accordion-body text-muted small">
                                Qualquer pessoa física ou jurídica, pública ou privada, que utilize, arrecade, guarde,
                                gerencie ou administre dinheiros, bens e valores públicos ou pelos quais a Administração
                                Pública responda, ou que, em nome desta, assuma obrigações de natureza pecuniária.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item border mb-2">
                        <h2 class="accordion-header" id="headingSix">
                            <button class="accordion-button bg-light text-secondary fw-medium collapsed" type="button"
                                data-bs-toggle="collapse" data-bs-target="#collapseSix">
                                Quem é o responsável pelas informações apresentadas no Portal da Transparência?
                            </button>
                        </h2>
                        <div id="collapseSix" class="accordion-collapse collapse" data-bs-parent="#accordionFaq">
                            <div class="accordion-body text-muted small">
                                A responsabilidade pela veracidade e integridade dos dados é de cada unidade gestora
                                (Prefeitura, Câmara, Autarquias ou Fundações) que gera a despesa ou receita. O controle
                                interno de cada órgão monitora a publicação dessas informações.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item border mb-2">
                        <h2 class="accordion-header" id="headingSeven">
                            <button class="accordion-button bg-light text-secondary fw-medium collapsed" type="button"
                                data-bs-toggle="collapse" data-bs-target="#collapseSeven">
                                Quero dar sugestões, fazer comentários ou críticas sobre o Portal. Como procedo?
                            </button>
                        </h2>
                        <div id="collapseSeven" class="accordion-collapse collapse" data-bs-parent="#accordionFaq">
                            <div class="accordion-body text-muted small">
                                O cidadão pode entrar em contato através da <strong>Ouvidoria Geral</strong>, utilizando o
                                sistema de e-SIC (Serviço de Informação ao Cidadão) disponível no menu principal, ou
                                presencialmente no balcão de atendimento da sede administrativa.
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <style>
        /* 1. Ajustes Gerais do Accordion */
        .accordion-button:focus {
            box-shadow: none;
            border-color: rgba(0, 0, 0, .125);
        }

        /* Estilo para quando o item estiver aberto (Modo Claro) */
        .accordion-button:not(.collapsed) {
            color: var(--bs-primary);
            /* Use a variável primária do sistema */
            background-color: rgba(var(--bs-primary-rgb), 0.05);
        }

        /* 2. Regras para o MODO ESCURO */
        body.dark-mode .card {
            background-color: #1e1e1e;
            border-color: #333;
        }

        body.dark-mode .accordion-item {
            background-color: #2d2d2d;
            border-color: #444;
        }

        body.dark-mode .accordion-button {
            background-color: #2d2d2d;
            color: #e0e0e0;
            /* Texto do título no modo escuro */
        }

        body.dark-mode .accordion-button:not(.collapsed) {
            background-color: #3d3d3d;
            color: #fff;
        }

        body.dark-mode .accordion-body {
            background-color: #1e1e1e;
            color: #b0b0b0 !important;
            /* Texto interno no modo escuro */
        }

        /* Garante que links e negritos fiquem visíveis no escuro */
        body.dark-mode .accordion-body strong {
            color: #fff;
        }

        body.dark-mode .accordion-body a {
            color: #4dabff;
        }

        /* Ajuste da seta do Accordion no modo escuro */
        body.dark-mode .accordion-button::after {
            filter: invert(1) grayscale(1) brightness(2);
        }
    </style>
@endsection
