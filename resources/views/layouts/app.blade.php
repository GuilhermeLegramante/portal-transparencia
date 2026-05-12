<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal da Transparência</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    @php $client = config('app.client_name', 'default'); @endphp
    <link rel="shortcut icon" href="{{ asset('img/' . $client . '.png') }}" type="image/x-icon">

    {{-- <link rel="stylesheet" href="{{ asset('css/app.css') }}"> --}}
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
</head>

<body>
    <a href="#conteudo" class="skip-link">Pular para o conteúdo principal</a>

    @php
        $nomeCliente = config('app.client_full_name', 'Prefeitura Municipal');
        $cnpjCliente = config('app.client_cnpj', '00.000.000/0000-00');
        $logoPath = public_path('img/' . config('app.client_name') . '.png');
        $logoBase64 = file_exists($logoPath)
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
            : '';

        // Busca a data da última publicação de forma eficiente
        $ultimaAtualizacao = \DB::table('glbclientepublicacao')
            ->where('idcliente', config('app.client_id'))
            ->max('datahora');

        // Formata a data para o padrão brasileiro, ou exibe a data atual como fallback
        $dataFormatada = $ultimaAtualizacao
            ? \Carbon\Carbon::parse($ultimaAtualizacao)->format('d/m/Y')
            : date('d/m/Y');
    @endphp

    <div class="accessibility-bar d-none d-md-block">
        <div class="container d-flex justify-content-between align-items-center py-1">
            <div class="access-links">
                <a href="#conteudo" accesskey="1" class="me-3 small text-decoration-none">Ir para o conteúdo [1]</a>
                <a href="#menu" accesskey="2" class="me-3 small text-decoration-none">Ir para o menu [2]</a>
            </div>
            <div class="access-controls d-flex gap-3">
                <div class="btn-group btn-group-sm">
                    <button id="btn-increase" class="btn btn-light border shadow-sm">A+</button>
                    <button id="btn-decrease" class="btn btn-light border shadow-sm">A-</button>
                </div>
                {{-- <button id="toggle-contrast" class="btn btn-sm btn-dark shadow-sm">
                    <i class="bi bi-circle-half"></i> Contraste
                </button> --}}
                <button id="toggle-dark-mode" class="btn btn-sm btn-outline-dark shadow-sm">
                    <i id="dark-mode-icon" class="bi bi-moon-stars"></i>
                    <span id="dark-mode-text" class="ms-1 d-none d-lg-inline">Modo Escuro</span>
                </button>
            </div>
        </div>
    </div>

    <div id="loader">
        <div class="spinner-grow text-primary" role="status"></div>
        <p class="mt-3 text-muted fw-medium">Sincronizando dados...</p>
    </div>

    @include('layouts.partials.topbar')
    <div class="sticky-top shadow-sm">
        @include('layouts.partials.navbar')
    </div>

    <main id="conteudo" class="py-5 min-vh-100">
        <div class="container">
            @yield('content')
        </div>
    </main>



    @include('layouts.partials.footer')



    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

    <script>
        const CLIENT_CONFIG = {
            name: @json($nomeCliente),
            cnpj: @json($cnpjCliente),
            logo: @json($logoBase64)
        };
    </script>

    <script src="{{ asset('js/accessibility.js') }}"></script>
    {{-- <script src="{{ asset('js/datatables-config.js') }}"></script> --}}
    <script src="{{ asset('js/ui-features.js') }}"></script>

    <script>
        /**
         * Concentra toda a lógica de estilização comum a todos os relatórios PDF
         */
        function aplicarConfiguracoesGlobaisPDF(doc) {
            // 1. Margens e Fonte Padrão
            doc.pageMargins = [40, 80, 40, 40];
            doc.defaultStyle.fontSize = 8;

            // 2. Estilo do Cabeçalho da Tabela
            doc.styles.tableHeader = {
                fillColor: '#f8fafc',
                color: '#475569',
                bold: true,
                fontSize: 8,
                alignment: 'left'
            };

            // 3. Configuração do Cabeçalho (Header) com Logo
            doc['header'] = function(currentPage, pageCount, pageSize) {
                return {
                    margin: [40, 20, 40, 0],
                    table: {
                        widths: [60, '*', 120],
                        body: [
                            [{
                                    image: CLIENT_CONFIG.logo,
                                    width: 50,
                                    alignment: 'left'
                                },
                                {
                                    stack: [{
                                            text: 'RELATÓRIO DE SISTEMA',
                                            fontSize: 12,
                                            bold: true,
                                            color: '#1e293b'
                                        },
                                        {
                                            text: CLIENT_CONFIG.name,
                                            fontSize: 9,
                                            bold: true,
                                            margin: [0, 2, 0, 0]
                                        },
                                        {
                                            text: 'CNPJ: ' + CLIENT_CONFIG.cnpj,
                                            fontSize: 8,
                                            color: '#64748b'
                                        }
                                    ],
                                    alignment: 'left'
                                }
                            ]
                        ]
                    },
                    layout: 'noBorders'
                };
            };

            // 4. Configuração do Rodapé (Footer)
            doc['footer'] = function(currentPage, pageCount) {
                return {
                    columns: [{
                            text: 'Gerado em: ' + window.location.href,
                            alignment: 'left',
                            margin: [40, 0],
                            fontSize: 7,
                            link: window.location.href
                        },
                        {
                            text: 'Página ' + currentPage.toString() + ' de ' + pageCount,
                            alignment: 'right',
                            margin: [0, 0, 40, 0],
                            fontSize: 7
                        }
                    ],
                    margin: [0, 10, 0, 0]
                };
            };

            // 5. Lógica de NoWrap para valores em "R$"
            doc.content.forEach(function(item) {
                if (item.table && item.table.body) {
                    item.table.body.forEach(function(linha) {
                        linha.forEach(function(celula) {
                            if (typeof celula.text === 'string' && celula.text.includes('R$')) {
                                celula.noWrap = true;
                            }
                        });
                    });
                }
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            const btnInc = document.getElementById('btn-increase');
            const btnDec = document.getElementById('btn-decrease');

            if (btnInc) {
                btnInc.addEventListener('click', () => changeFontSize('increase'));
            }
            if (btnDec) {
                btnDec.addEventListener('click', () => changeFontSize('decrease'));
            }
        });

        function changeFontSize(action) {
            const el = document.documentElement;
            let size = parseFloat(window.getComputedStyle(el).fontSize);
            if (action === 'increase' && size < 24) el.style.fontSize = (size + 2) + 'px';
            if (action === 'decrease' && size > 12) el.style.fontSize = (size - 2) + 'px';
        }

        $(document).ready(function() {
            const COOKIE_KEY = 'portal_transparencia_cookies';
            const $banner = $('#cookie-banner');

            // 1. Lógica de exibição invertida (mais segura)
            if (!localStorage.getItem(COOKIE_KEY)) {
                console.log("LGPD: Exibindo banner...");
                // Adicionamos a animação e removemos o d-none ao mesmo tempo
                $banner.addClass('animate__animated animate__fadeInUp').removeClass('d-none').show();
            } else {
                console.log("LGPD: Já aceito. Removendo do DOM.");
                $banner.remove(); // Remove o elemento para não ocupar memória
            }

            // 2. Evento de clique
            $(document).on('click', '#btn-accept-cookies', function(e) {
                e.preventDefault();
                try {
                    localStorage.setItem(COOKIE_KEY, 'true');

                    // Troca animação de entrada pela de saída
                    $banner.removeClass('animate__fadeInUp').addClass('animate__fadeOutDown');

                    setTimeout(function() {
                        $banner.remove();
                    }, 500);
                } catch (error) {
                    $banner.hide();
                }
            });

            $('.js-datatable').each(function() {
                // 1. Captura correta do título (pegando o h5)
                var tituloCard = $(this).closest('.card').find('.card-header h5').text().trim();

                var nomeCliente = {!! json_encode($nomeCliente) !!};
                var cnpjCliente = {!! json_encode($cnpjCliente) !!};

                // Captura a orientação vinda do PHP (padrão 'landscape' se não vier nada)
                const orientacaoPDF = {!! json_encode($orientacaoPDF ?? 'landscape') !!};

                if (!$.fn.DataTable.isDataTable(this)) {
                    $(this).DataTable({
                        "order": [], // <-- Garante que o plugin não aplique nenhuma ordenação automática
                        "language": {
                            "url": "https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json"
                        },
                        "pageLength": 25,
                        "dom": '<"d-flex justify-content-between align-items-center mb-2" <"d-flex align-items-center"B l> f>rtip',
                        "buttons": [{
                                extend: 'excelHtml5',
                                // Usamos span para melhor controle de estilo do ícone
                                text: '<span class="d-flex align-items-center"><i class="fas fa-file-excel fs-6 me-2"></i> EXCEL</span>',
                                className: 'btn btn-modern-excel btn-sm shadow-sm me-2',
                                title: tituloCard,
                                filename: 'Exportacao_' + tituloCard.replace(/\s+/g, '_'),
                                footer: true,
                                // FILTRO: Exporta apenas colunas que NÃO tenham o título "Ação"
                                exportOptions: {
                                    columns: function(idx, data, node) {
                                        // Pega o texto do th correspondente ao índice da coluna
                                        let headerText = $(node).closest('table').find(
                                            'thead th').eq(idx).text().trim();

                                        // Retorna falso (não exporta) se:
                                        // 1. O texto for "Ação" ou "Ações"
                                        // 2. O texto for vazio (colunas sem título)
                                        return headerText !== "" && headerText !== "Ação" &&
                                            headerText !== "Ações";
                                    }
                                }
                            },
                            {
                                extend: 'pdfHtml5',
                                text: '<span class="d-flex align-items-center"><i class="fas fa-file-pdf fs-6 me-2"></i> PDF</span>',
                                className: 'btn btn-modern-pdf btn-sm shadow-sm me-2',
                                title: '',
                                footer: true,
                                orientation: orientacaoPDF,
                                exportOptions: {
                                    columns: function(idx, data, node) {
                                        // Pega o texto do th correspondente ao índice da coluna
                                        let headerText = $(node).closest('table').find(
                                            'thead th').eq(idx).text().trim();

                                        // Retorna falso (não exporta) se:
                                        // 1. O texto for "Ação" ou "Ações"
                                        // 2. O texto for vazio (colunas sem título)
                                        return headerText !== "" && headerText !== "Ação" &&
                                            headerText !== "Ações";
                                    }
                                },
                                filename: function() {
                                    const dataHora = new Date().toLocaleString('pt-BR')
                                        .replace(/[\/:]/g, '-').replace(/, /g, '_');
                                    return tituloCard.replace(/[\s/]+/g, '_') + '_' +
                                        dataHora;
                                },
                                customize: function(doc) {
                                    // Aplica o padrão visual de todos os relatórios
                                    aplicarConfiguracoesGlobaisPDF(doc);

                                    // Se a página específica (ex: detalhe.blade) definiu uma função extra, executa-a
                                    if (typeof personalizarPDFEspecifico === 'function') {
                                        personalizarPDFEspecifico(doc);
                                    }
                                }
                            }
                        ]
                    });
                }
            });
        });
    </script>
</body>

</html>
