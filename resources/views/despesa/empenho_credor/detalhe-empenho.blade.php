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

    <script>
        function personalizarPDFEspecifico(doc) {
            // 1. Definição dos dados capturados do PHP
            const credorNome = "{{ $credor->nome }}";
            const credorDocumento = "{{ $credor->tipo_pessoa == 'F' ? $credor->cpf : $credor->cnpj }}";

            const empenhoNumero = "{{ $empenho->numero }} / {{ $exercicio }}";
            const empenhoEmissao = "{{ date('d/m/Y', strtotime($empenho->dataemissao)) }}";
            const empenhoElemento = "{{ $empenho->elemento }}";
            const empenhoModalidade = "{{ $empenho->modalidade }}";

            // 2. Criação do bloco combinado (Empenho + Credor)
            var blocoInformacoes = {
                margin: [0, 0, 0, 15],
                table: {
                    widths: ['*', '*'], // Divide a página ao meio
                    body: [
                        // Títulos das colunas
                        [{
                                text: 'DADOS DO EMPENHO',
                                fontSize: 9,
                                bold: true,
                                color: '#0d6efd',
                                margin: [0, 0, 0, 5]
                            },
                            {
                                text: 'IDENTIFICAÇÃO DO CREDOR',
                                fontSize: 9,
                                bold: true,
                                color: '#0d6efd',
                                margin: [0, 0, 0, 5]
                            }
                        ],
                        // Conteúdo dos quadros
                        [{
                                fillColor: '#f1f5f9',
                                padding: [10, 10, 10, 10],
                                stack: [{
                                        text: 'Número/Exercício: ' + empenhoNumero,
                                        fontSize: 8,
                                        bold: true,
                                        margin: [0, 2]
                                    },
                                    {
                                        text: 'Data Emissão: ' + empenhoEmissao,
                                        fontSize: 8,
                                        margin: [0, 2]
                                    },
                                    {
                                        text: 'Modalidade: ' + empenhoModalidade,
                                        fontSize: 8,
                                        margin: [0, 2]
                                    },
                                    {
                                        text: 'Elemento: ' + empenhoElemento,
                                        fontSize: 8,
                                        color: '#1e293b',
                                        bold: true,
                                        margin: [0, 2]
                                    }
                                ]
                            },
                            {
                                fillColor: '#f1f5f9',
                                padding: [10, 10, 10, 10],
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
                                        text: credorDocumento,
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

            // 3. Inserção no início do conteúdo (antes da tabela de itens)
            doc.content.splice(0, 0, blocoInformacoes);

            // 4. Ajuste de larguras da tabela de itens para ocupar 100% da página
            doc.content.forEach(function(item) {
                if (item.table && item.table.body && item.table.body[0].length > 1) {
                    // Colunas: Número(auto), Descrição(*), Quantidade(auto), Unitário(auto), Total(auto)
                    item.table.widths = ['auto', '*', 'auto', 'auto', 'auto'];
                }
            });
        }
    </script>
@endsection
