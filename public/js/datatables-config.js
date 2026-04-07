$('.js-datatable').each(function () {
    // Captura o título do card-header mais próximo para usar na exportação
    var tituloCard = $(this).closest('.card').find('.card-header span').text().trim();

    // Captura as variáveis PHP para o JS de forma segura
    var nomeCliente = {!! json_encode($nomeCliente)!!
    var cnpjCliente = {!! json_encode($cnpjCliente)!!

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
                filename: function () {
                    // Usamos a variável tituloCard que foi capturada no início do loop .each()
                    // Isso evita erros de escopo com o 'this'
                    var nomeArquivo = tituloCard.replace(/[\s/]+/g, '_');

                    var agora = new Date();
                    var dataHora = agora.toLocaleDateString('pt-BR')
                        .replace(/\//g, '-') + '_' +
                        agora.getHours() + 'h' + agora.getMinutes();

                    return nomeArquivo + '_' + dataHora;
                },
                customize: function (doc) {
                    // Localizar a tabela de dados dentro do documento
                    var tabelaDados;

                    doc.content.forEach(function (item) {
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
                        footerRow.slice().reverse().forEach(function (
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
                            hLineWidth: function (i) {
                                return 0.5;
                            },
                            vLineWidth: function (i) {
                                return 0;
                            },
                            hLineColor: function (i) {
                                return '#aaa';
                            },
                            paddingLeft: function (i) {
                                return 4;
                            },
                            paddingRight: function (i) {
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
                    objLayout['hLineWidth'] = function (i) {
                        return (i === 1 || i === doc.content[1].table
                            .body.length) ? 1 : 0.5;
                    };
                    objLayout['vLineWidth'] = function (i) {
                        return 0;
                    }; // Remove linhas verticais como na imagem
                    objLayout['hLineColor'] = function (i) {
                        return '#333';
                    };
                    doc.content[1].layout = objLayout;
                }
            }
            ],
            "drawCallback": function () {
                $('.dataTables_filter input').addClass(
                    'form-control form-control-sm ms-2');
                $('.dataTables_length select').addClass(
                    'form-select form-select-sm ms-2 me-2');
            }
        });
    }
