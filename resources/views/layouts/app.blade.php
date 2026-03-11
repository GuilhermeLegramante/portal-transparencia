<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal da Transparência</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @php
        $client = env('CLIENT_NAME', 'default');
    @endphp
    <link rel="shortcut icon" href="{{ asset('img/' . $client . '.png') }}" type="image/x-icon">
    <style>
        /* Estilos personalizados para aproximar o design da imagem */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #fcfcfc;
        }

        .top-bar {
            font-size: 0.85rem;
            background-color: #f8f9fa;
            border-bottom: 1px solid #ddd;
        }

        .nav-link {
            color: #555;
            font-weight: 500;
        }

        .nav-link:hover,
        .nav-link.active {
            color: #d9534f;
            border-bottom: 2px solid #d9534f;
        }

        /* Cores dos Cards de Resumo */
        .card-green {
            background-color: #4CAF50;
            color: white;
        }

        .card-red {
            background-color: #E53935;
            color: white;
        }

        .card-orange {
            background-color: #FFB300;
            color: white;
        }

        .card-blue {
            background-color: #42A5F5;
            color: white;
        }

        .summary-card {
            padding: 20px;
            text-align: right;
            border-radius: 0;
            border: none;
            position: relative;
            overflow: hidden;
        }

        .summary-card h2 {
            margin-bottom: 0;
            font-size: 2.5rem;
            font-weight: 300;
        }

        .summary-card p {
            margin-bottom: 0;
            font-size: 0.9rem;
            opacity: 0.9;
        }

        /* Rodapé */
        footer {
            background-color: #333;
            color: #ccc;
            font-size: 0.9rem;
        }

        footer h5 {
            color: white;
            font-weight: normal;
            margin-bottom: 15px;
        }

        .footer-bottom {
            background-color: #222;
            font-size: 0.8rem;
            padding: 15px 0;
        }

        /* Estilo para permitir submenus laterais */
        .dropdown-submenu {
            position: relative;
        }

        .dropdown-submenu .dropdown-menu {
            top: 0;
            left: 100%;
            margin-top: -1px;
            border-radius: 0;
            display: none;
            /* Escondido por padrão */
        }

        /* Mostra o submenu ao passar o mouse no item pai */
        .dropdown-submenu:hover>.dropdown-menu {
            display: block;
        }

        /* Estilização para ficar igual à imagem (Laranja) */
        .dropdown-item:hover {
            background-color: #f15a24;
            /* Laranja da imagem */
            color: white;
        }

        .dropdown-item.active,
        .dropdown-item:active {
            background-color: #f15a24;
        }

        /* Ajuste na seta do submenu */
        .dropdown-submenu>a::after {
            display: inline-block;
            float: right;
            margin-top: 5px;
            content: "\f105";
            /* Ícone de seta do Font Awesome */
            font-family: "Font Awesome 6 Free";
            font-weight: 600;
            border: none;
        }
    </style>

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">


</head>

<body>
    @php
        // Dados do Cliente
        $client = env('CLIENT_NAME', 'default');
        $nomeCliente = env('CLIENT_FULL_NAME', 'Prefeitura Municipal');
        $cnpjCliente = env('CLIENT_CNPJ', '00.000.000/0000-00');

        // Busca o logo
        $logoPath = public_path('img/' . $client . '.png');
        $logoBase64 = '';
        if (file_exists($logoPath)) {
            $logoData = file_get_contents($logoPath);
            $logoBase64 = 'data:image/png;base64,' . base64_encode($logoData);
        }
    @endphp

    @include('layouts.partials.topbar')

    @include('layouts.partials.navbar')

    <main class="py-4">
        @yield('content')
    </main>

    @include('layouts.partials.footer')

    @stack('scripts')

    <script src="{{ asset('js/highcharts.js') }}"></script>

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function() {
            $('.js-datatable').each(function() {
                // Captura o título do card-header mais próximo para usar na exportação
                var tituloCard = $(this).closest('.card').find('.card-header span').text().trim();

                // Captura as variáveis PHP para o JS de forma segura
                var nomeCliente = {!! json_encode($nomeCliente) !!};
                var cnpjCliente = {!! json_encode($cnpjCliente) !!};

                if (!$.fn.DataTable.isDataTable(this)) {
                    $(this).DataTable({
                        "language": {
                            "url": "https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json"
                        },
                        "pageLength": 25,
                        "order": [],
                        "dom": '<"d-flex justify-content-between align-items-center mb-2" <"d-flex align-items-center"B l> f>rtip',

                        "buttons": [{
                                extend: 'excelHtml5',
                                text: '<i class="fa fa-file-excel me-1"></i> Excel',
                                className: 'btn btn-success btn-sm border-0 shadow-sm me-1',
                                title: tituloCard, // Título da planilha (dentro do arquivo)
                                filename: 'Exportacao_' + tituloCard.replace(/\s+/g,
                                    '_'), // Nome do arquivo .xlsx
                                footer: true,
                                messageTop: 'Relatório gerado em: ' + new Date().toLocaleString(
                                    'pt-BR')
                            },
                            {
                                extend: 'pdfHtml5',
                                text: '<i class="fa fa-file-pdf me-1"></i> PDF',
                                className: 'btn btn-danger btn-sm border-0 shadow-sm me-1',
                                title: '', // Deixamos vazio para construir o título manualmente no customize
                                exportOptions: {
                                    columns: ':visible'
                                },
                                footer: true,
                                orientation: 'portrait',
                                // Configuração do Nome do Arquivo
                                filename: function() {
                                    // Usamos a variável tituloCard que foi capturada no início do loop .each()
                                    // Isso evita erros de escopo com o 'this'
                                    var nomeArquivo = tituloCard.replace(/[\s/]+/g, '_');

                                    var agora = new Date();
                                    var dataHora = agora.toLocaleDateString('pt-BR')
                                        .replace(/\//g, '-') + '_' +
                                        agora.getHours() + 'h' + agora.getMinutes();

                                    return nomeArquivo + '_' + dataHora;
                                },
                                customize: function(doc) {
                                    // Localizar a tabela de dados dentro do documento
                                    var tabelaDados;

                                    doc.content.forEach(function(item) {
                                        if (item.table && item.table.body) {
                                            // Ignoramos a tabela do cabeçalho (que tem o logo) 
                                            // identificando-a pela quantidade de colunas ou conteúdo
                                            if (item.table.body[0].length > 3) {
                                                tabelaDados = item;
                                            }
                                        }
                                    });

                                    if (tabelaDados) {
                                        // 1. OBRIGA A TABELA A OCUPAR 100% DA LARGURA
                                        // O '*' distribui as colunas proporcionalmente
                                        tabelaDados.table.widths = Array(tabelaDados.table
                                            .body[0].length).fill('*');

                                        // 2. LIMPAR "TOTAIS" REPETIDOS NO RODAPÉ
                                        var footerRow = tabelaDados.table.body[tabelaDados
                                            .table.body.length - 1];
                                        var encontrouPrimeiro = false;

                                        // Inverte a lógica: percorre as células do final para o início
                                        var footerRow = tabelaDados.table.body[tabelaDados
                                            .table.body.length - 1];
                                        var encontrouUltimo = false;

                                        // Clonamos o array e invertemos para achar o "primeiro de trás pra frente"
                                        footerRow.slice().reverse().forEach(function(
                                        celula) {
                                            if (celula.text && celula.text.includes(
                                                    'TOTAIS')) {
                                                if (!encontrouUltimo) {
                                                    // Este é o último "TOTAIS" da linha original, mantém ele
                                                    encontrouUltimo = true;
                                                } else {
                                                    // Já encontramos o último, então limpa os anteriores
                                                    celula.text = '';
                                                }
                                            }
                                        });

                                        // 3. REMOVE LINHAS VERTICAIS E AJUSTA BORDAS (Estilo da imagem)
                                        tabelaDados.layout = {
                                            hLineWidth: function(i) {
                                                return 0.5;
                                            },
                                            vLineWidth: function(i) {
                                                return 0;
                                            },
                                            hLineColor: function(i) {
                                                return '#aaa';
                                            },
                                            paddingLeft: function(i) {
                                                return 4;
                                            },
                                            paddingRight: function(i) {
                                                return 4;
                                            }
                                        };
                                    }

                                    // 1. ZERAR ESTILOS GERAIS PARA FORÇAR O NOSSO
                                    doc.defaultStyle.fontSize = 6;

                                    // Estilização da tabela de dados para ficar "limpa" como a da imagem
                                    doc.styles.tableHeader = {
                                        fillColor: '#ffffff',
                                        color: '#000000',
                                        bold: true,
                                        fontSize: 6,
                                        alignment: 'left',
                                        border: [false, true, false,
                                            true
                                        ] // Linhas apenas em cima e embaixo
                                    };

                                    // 3. DIMINUIR A FONTE DO RODAPÉ (TOTAIS)
                                    doc.styles.tableFooter = {
                                        fontSize: 6,
                                        bold: true
                                    };

                                    var logo =
                                        "{{ $logoBase64 }}"; // Variável PHP convertida

                                    // Criamos um cabeçalho personalizado com 3 colunas (Logo | Texto | Info)
                                    doc.content.splice(0, 0, {
                                        margin: [0, 0, 0, 12],
                                        table: {
                                            widths: [80, '*', 150],
                                            body: [
                                                [{
                                                        image: logo,
                                                        width: 60,
                                                        alignment: 'left'
                                                    },
                                                    {
                                                        stack: [{
                                                                text: tituloCard
                                                                    .toUpperCase(),
                                                                fontSize: 16,
                                                                bold: true,
                                                                margin: [0,
                                                                    5,
                                                                    0, 0
                                                                ]
                                                            },
                                                            {
                                                                text: nomeCliente, // VARIÁVEL DINÂMICA
                                                                fontSize: 12,
                                                                bold: true
                                                            },
                                                            {
                                                                text: 'CNPJ: ' +
                                                                    cnpjCliente, // VARIÁVEL DINÂMICA
                                                                fontSize: 8
                                                            }
                                                        ],
                                                        alignment: 'left'
                                                    },
                                                    {
                                                        stack: [{
                                                                text: 'Página: 1/1',
                                                                alignment: 'right',
                                                                fontSize: 6
                                                            },
                                                            {
                                                                text: '\nEmissão: ' +
                                                                    new Date()
                                                                    .toLocaleString(
                                                                        'pt-BR'
                                                                    ),
                                                                alignment: 'right',
                                                                fontSize: 6
                                                            }
                                                        ]
                                                    }
                                                ]
                                            ]
                                        },
                                        layout: 'noBorders' // Remove as bordas apenas deste cabeçalho
                                    });



                                    // Ajusta as bordas da tabela principal
                                    var objLayout = {};
                                    objLayout['hLineWidth'] = function(i) {
                                        return (i === 1 || i === doc.content[1].table
                                            .body.length) ? 1 : 0.5;
                                    };
                                    objLayout['vLineWidth'] = function(i) {
                                        return 0;
                                    }; // Remove linhas verticais como na imagem
                                    objLayout['hLineColor'] = function(i) {
                                        return '#333';
                                    };
                                    doc.content[1].layout = objLayout;
                                }
                            }
                        ],
                        "drawCallback": function() {
                            $('.dataTables_filter input').addClass(
                                'form-control form-control-sm ms-2');
                            $('.dataTables_length select').addClass(
                                'form-select form-select-sm ms-2 me-2');
                        }
                    });
                }
            });
        });

        // Selecionamos o nosso carrossel pelo ID
        const myCarousel = document.getElementById('portalCarousel');

        // Este evento do Bootstrap dispara toda vez que um slide começa a transição
        myCarousel.addEventListener('slide.bs.carousel', event => {

            // Buscamos todos os elementos que têm classes de animação dentro do carrossel
            const animatedElements = event.target.querySelectorAll('.animate__animated');

            animatedElements.forEach(el => {
                // Descobrimos qual era a animação original (ex: animate__fadeInLeft)
                const animationClass = Array.from(el.classList).find(cl => cl.startsWith('animate__'));

                // Removemos a animação para "resetar" o elemento
                el.classList.remove(animationClass);

                // Forçamos o navegador a registrar a remoção e reiniciamos a animação
                // Isso é um truque técnico (void el.offsetWidth) para reiniciar o ciclo de CSS
                void el.offsetWidth;
                el.classList.add(animationClass);
            });
        });
    </script>


</body>

</html>
