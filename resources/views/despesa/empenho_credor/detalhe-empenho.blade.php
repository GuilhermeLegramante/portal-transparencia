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
            try {
                // 1. Captura segura dos dados (ajustado para as variáveis do seu detalhe-empenho.blade.php)
                const credorNome = "{{ $credor->nome }}";
                const credorDoc = "{{ $credor->tipo_pessoa == 'F' ? $credor->cpf : $credor->cnpj }}";
                const empenhoNum = "{{ $empenho->numero }} / {{ $exercicio }}";
                const empenhoEmi = "{{ date('d/m/Y', strtotime($empenho->dataemissao)) }}";
                const empenhoElem = "{{ $empenho->elemento }}";

                // 2. Criação do quadro
                var quadroCombinado = {
                    margin: [0, 0, 0, 15],
                    table: {
                        // Usar porcentagens em vez de '*' evita o erro NaN em muitos navegadores
                        widths: ['48%', '48%'],
                        body: [
                            [{
                                    text: 'DADOS DO EMPENHO',
                                    fontSize: 9,
                                    bold: true,
                                    color: '#0d6efd'
                                },
                                {
                                    text: 'IDENTIFICAÇÃO DO CREDOR',
                                    fontSize: 9,
                                    bold: true,
                                    color: '#0d6efd'
                                }
                            ],
                            [{
                                    fillColor: '#f8fafc',
                                    padding: [8, 8, 8, 8],
                                    stack: [{
                                            text: 'Nº/Exercício: ' + empenhoNum,
                                            fontSize: 8,
                                            bold: true
                                        },
                                        {
                                            text: 'Emissão: ' + empenhoEmi,
                                            fontSize: 8
                                        },
                                        {
                                            text: 'Elemento: ' + empenhoElem,
                                            fontSize: 8
                                        }
                                    ]
                                },
                                {
                                    fillColor: '#f8fafc',
                                    padding: [8, 8, 8, 8],
                                    stack: [{
                                            text: 'Nome: ' + credorNome,
                                            fontSize: 8,
                                            bold: true
                                        },
                                        {
                                            text: 'Documento: ' + credorDoc,
                                            fontSize: 8
                                        }
                                    ]
                                }
                            ]
                        ]
                    },
                    layout: 'noBorders'
                };

                // 3. Inserir no topo
                if (doc.content && Array.isArray(doc.content)) {
                    doc.content.splice(0, 0, quadroCombinado);
                }

                // 4. Ajuste da tabela principal (Itens) para evitar NaN no widths
                doc.content.forEach(function(item) {
                    if (item.table && item.table.body && item.table.body[0].length > 1) {
                        // Forçamos larguras fixas ou seguras para a tabela de itens
                        // Total de 5 colunas no empenho: Número, Descrição, Qtd, Unit, Total
                        item.table.widths = [40, '*', 60, 70, 70];
                    }
                });

            } catch (err) {
                console.error("Erro ao personalizar PDF:", err);
            }
        }
    </script>
@endsection
