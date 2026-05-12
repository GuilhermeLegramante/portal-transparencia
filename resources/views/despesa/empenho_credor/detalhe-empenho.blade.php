@extends('layouts.app')

@section('content')
    <style>
        /* Força o comportamento correto da alternância sem quebrar o layout */
        .nav-tabs .nav-link.active {
            color: #0d6efd !important;
            /* Texto Azul quando ativa */
            background-color: #fff !important;
            /* Fundo Branco quando ativa */
        }

        .nav-tabs .nav-link:not(.active) {
            color: #ffffff !important;
            /* Texto Branco quando inativa */
            background-color: transparent !important;
            /* Fundo transparente quando inativa */
        }
    </style>
    <div class="container">
        <x-breadcrumb :items="[
            'Despesa' => '#',
            'Empenho Orçamentário' => route('empenho.credor.index'),
            'Credores de ' . $exercicio => route('empenho.credor.lista', $exercicio),
            'Credor: ' . $credor->nome => '',
            'Empenho: ' . $empenho->numero => '',
        ]" />

        @include('layouts.partials.credor')

        @include('layouts.partials.empenho')

        <a href="{{ route('empenho.credor.empenho.detalhe', [
            'exercicio' => $exercicio,
            'credor_id' => $credor->inscricao, // Ajustado de id para inscricao
            'empenho_id' => $empenho->id,
        ]) }}"
            class="btn btn-secondary shadow-sm">
            <i class="fa fa-arrow-left"></i> Voltar
        </a>
    </div>

    {{-- <script>
        /**
         * Injeta blocos de Credor e Empenho no topo do PDF
         */
        function personalizarPDFEspecifico(doc) {
            // --- DADOS DO EMPENHO ---
            const nEmpenho = "{{ $empenho->numero }} / {{ $exercicio }}";
            const dataEmissao = "{{ date('d/m/Y', strtotime($empenho->dataemissao)) }}";
            const modalidade = "{{ $empenho->modalidade }} / {{ $empenho->especie }}";
            const elemento = "{{ $empenho->elemento }}";

            // --- DADOS DO CREDOR ---
            const credorNome = "{{ $empenho->nome_municipe }}";
            const credorDoc = "{{ $empenho->documento }}";

            // Bloco Combinado: Empenho e Credor
            var cabecalhoDetalhado = {
                margin: [0, 0, 0, 15],
                table: {
                    widths: ['*', '*'],
                    body: [
                        // Linha 1: Títulos dos Quadros
                        [{
                                text: 'IDENTIFICAÇÃO DO EMPENHO',
                                fontSize: 9,
                                bold: true,
                                color: '#0d6efd',
                                margin: [0, 0, 0, 5]
                            },
                            {
                                text: 'DADOS DO CREDOR',
                                fontSize: 9,
                                bold: true,
                                color: '#0d6efd',
                                margin: [0, 0, 0, 5]
                            }
                        ],
                        // Linha 2: Conteúdo em colunas
                        [{
                                fillColor: '#f8fafc',
                                padding: [10, 8, 10, 8],
                                stack: [{
                                        text: 'Número/Exercício: ' + nEmpenho,
                                        fontSize: 8,
                                        margin: [0, 2]
                                    },
                                    {
                                        text: 'Emissão: ' + dataEmissao,
                                        fontSize: 8,
                                        margin: [0, 2]
                                    },
                                    {
                                        text: 'Modalidade: ' + modalidade,
                                        fontSize: 8,
                                        margin: [0, 2]
                                    },
                                    {
                                        text: 'Elemento: ' + elemento,
                                        fontSize: 8,
                                        bold: true,
                                        color: '#1e293b'
                                    }
                                ]
                            },
                            {
                                fillColor: '#f8fafc',
                                padding: [10, 8, 10, 8],
                                stack: [{
                                        text: 'Nome / Razão Social:',
                                        fontSize: 7,
                                        color: '#64748b'
                                    },
                                    {
                                        text: credorNome,
                                        fontSize: 9,
                                        bold: true,
                                        color: '#1e293b',
                                        margin: [0, 0, 0, 4]
                                    },
                                    {
                                        text: 'CPF/CNPJ:',
                                        fontSize: 7,
                                        color: '#64748b'
                                    },
                                    {
                                        text: credorDoc,
                                        fontSize: 9,
                                        color: '#1e293b'
                                    }
                                ]
                            }
                        ]
                    ]
                },
                layout: 'noBorders'
            };

            // Injeta antes da tabela de itens
            doc.content.splice(0, 0, cabecalhoDetalhado);

            // Ajuste opcional: Forçar a tabela de itens a ocupar 100% da largura
            doc.content.forEach(function(item) {
                if (item.table && item.table.body && item.table.body[0].length > 1) {
                    // No empenho.blade as colunas são: Número, Descrição, Qtd, Unitário, Total
                    item.table.widths = ['auto', '*', 'auto', 'auto', 'auto'];
                }
            });
        }
    </script> --}}

    <script>
        /**
         * Esta função é chamada pelo customize do PDF no app.blade.php
         * Ela injeta os dados do credor no topo do documento.
         */
        function personalizarPDFEspecifico(doc) {
            // 1. Definição dos dados capturados do PHP
            const credorNome = "{{ $credor->nome }}";
            const credorDocumento = "{{ $credor->tipo_pessoa == 'F' ? $credor->cpf : $credor->cnpj }}";
            const credorInscricao = "{{ $credor->inscricao }}";
            const credorEndereco =
                "{{ $credor->nome_logradouro ?? 'Não informado' }}, {{ $credor->numero_imovel ?? 'S/N' }}";

            // 2. Criação do quadro de informações do Credor
            var quadroCredor = {
                margin: [0, 0, 0, 15], // Margem inferior para separar da tabela
                table: {
                    widths: ['*'],
                    body: [
                        [{
                            fillColor: '#f1f5f9', // Fundo cinza suave (estilo Bootstrap soft-blue)
                            padding: [12, 10, 12, 10],
                            stack: [{
                                    text: 'IDENTIFICAÇÃO DO CREDOR',
                                    fontSize: 10,
                                    bold: true,
                                    color: '#0d6efd',
                                    margin: [0, 0, 0, 8]
                                },
                                {
                                    columns: [{
                                            width: '*',
                                            stack: [{
                                                    text: 'Nome / Razão Social:',
                                                    fontSize: 7,
                                                    color: '#64748b',
                                                    bold: true
                                                },
                                                {
                                                    text: credorNome,
                                                    fontSize: 9,
                                                    bold: true,
                                                    color: '#1e293b'
                                                }
                                            ]
                                        },
                                        {
                                            width: 'auto',
                                            stack: [{
                                                    text: 'CPF/CNPJ:',
                                                    fontSize: 7,
                                                    color: '#64748b',
                                                    bold: true
                                                },
                                                {
                                                    text: credorDocumento,
                                                    fontSize: 9,
                                                    color: '#1e293b'
                                                }
                                            ]
                                        }
                                    ]
                                },
                                {
                                    margin: [0, 8, 0, 0],
                                    columns: [{
                                            width: '*',
                                            stack: [{
                                                    text: 'Endereço:',
                                                    fontSize: 7,
                                                    color: '#64748b',
                                                    bold: true
                                                },
                                                {
                                                    text: credorEndereco,
                                                    fontSize: 8,
                                                    color: '#1e293b'
                                                }
                                            ]
                                        },
                                        {
                                            width: 'auto',
                                            stack: [{
                                                    text: 'Inscrição:',
                                                    fontSize: 7,
                                                    color: '#64748b',
                                                    bold: true
                                                },
                                                {
                                                    text: credorInscricao,
                                                    fontSize: 8,
                                                    color: '#1e293b'
                                                }
                                            ],
                                            margin: [20, 0, 0, 0]
                                        }
                                    ]
                                }
                            ]
                        }]
                    ]
                },
                layout: 'noBorders'
            };

            // 3. Inserção no início do conteúdo (antes da tabela principal)
            doc.content.splice(0, 0, quadroCredor);
        }
    </script>
@endsection
