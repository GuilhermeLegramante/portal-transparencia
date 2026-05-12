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
        /**
         * Esta função injeta os dados do Credor e do Empenho no topo do PDF.
         */
        function personalizarPDFEspecifico(doc) {
            // 1. Definição dos dados capturados do PHP
            const credorNome = "{{ $credor->nome }}";
            const credorDocumento = "{{ $credor->tipo_pessoa == 'F' ? $credor->cpf : $credor->cnpj }}";
            const credorInscricao = "{{ $credor->inscricao }}";
            const credorEndereco =
                "{{ $credor->nome_logradouro ?? 'Não informado' }}, {{ $credor->numero_imovel ?? 'S/N' }}";

            const empenhoNum = "{{ $empenho->numero }} / {{ $exercicio }}";
            const empenhoEmi = "{{ date('d/m/Y', strtotime($empenho->dataemissao)) }}";
            const empenhoElem = "{{ $empenho->elemento }}";
            const empenhoMod = "{{ $empenho->modalidade }}";

            // 2. Quadro do Credor
            var quadroCredor = {
                margin: [0, 0, 0, 5],
                table: {
                    widths: ['100%'],
                    body: [
                        [{
                            fillColor: '#f1f5f9',
                            padding: [10, 8, 10, 8],
                            stack: [{
                                    text: 'IDENTIFICAÇÃO DO CREDOR',
                                    fontSize: 9,
                                    bold: true,
                                    color: '#0d6efd',
                                    margin: [0, 0, 0, 4]
                                },
                                {
                                    columns: [{
                                            text: 'Nome: ' + credorNome,
                                            fontSize: 8,
                                            bold: true,
                                            width: '*'
                                        },
                                        {
                                            text: 'Doc: ' + credorDocumento,
                                            fontSize: 8,
                                            width: 'auto'
                                        }
                                    ]
                                },
                                {
                                    text: 'Endereço: ' + credorEndereco,
                                    fontSize: 7,
                                    color: '#64748b',
                                    margin: [0, 2, 0, 0]
                                }
                            ]
                        }]
                    ]
                },
                layout: 'noBorders'
            };

            // 3. Quadro do Empenho (Logo abaixo)
            var quadroEmpenho = {
                margin: [0, 0, 0, 15],
                table: {
                    widths: ['100%'],
                    body: [
                        [{
                            fillColor: '#f8fafc', // Tom levemente diferente para distinguir
                            padding: [10, 8, 10, 8],
                            stack: [{
                                    text: 'DADOS DO EMPENHO',
                                    fontSize: 9,
                                    bold: true,
                                    color: '#0d6efd',
                                    margin: [0, 0, 0, 4]
                                },
                                {
                                    columns: [{
                                            text: 'Nº/Exercício: ' + empenhoNum,
                                            fontSize: 8,
                                            bold: true,
                                            width: '*'
                                        },
                                        {
                                            text: 'Emissão: ' + empenhoEmi,
                                            fontSize: 8,
                                            width: 'auto'
                                        }
                                    ]
                                },
                                {
                                    margin: [0, 2, 0, 0],
                                    columns: [{
                                            text: 'Elemento: ' + empenhoElem,
                                            fontSize: 8,
                                            width: '*'
                                        },
                                        {
                                            text: 'Modalidade: ' + empenhoMod,
                                            fontSize: 8,
                                            width: 'auto'
                                        }
                                    ]
                                }
                            ]
                        }]
                    ]
                },
                layout: 'noBorders'
            };

            // 4. Inserção no PDF (Empenho entra depois do Credor)
            // Usamos splice para garantir que fiquem no topo antes da tabela de itens
            doc.content.splice(0, 0, quadroCredor, quadroEmpenho);

            // 5. Ajuste preventivo para a tabela de itens não dar NaN
            doc.content.forEach(function(item) {
                if (item.table && item.table.body && item.table.body[0].length > 1) {
                    // Força as larguras da tabela principal (Itens)
                    item.table.widths = [40, '*', 60, 80, 80];
                }
            });
        }
    </script>
@endsection
