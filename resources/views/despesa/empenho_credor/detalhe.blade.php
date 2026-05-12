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
    <div class="container py-4">
        <x-breadcrumb :items="[
            'Despesa' => '#',
            'Empenho Orçamentário' => route('empenho.credor.index'),
            'Credores de ' . $exercicio => route('empenho.credor.lista', $exercicio),
            $credor->nome => '',
        ]" />

        @include('layouts.partials.credor')

        @php
            $columns = [
                ['label' => '', 'icone' => 'fa-search', 'align' => 'text-center'], // Coluna da Lupa
                ['label' => 'Número', 'icone' => '', 'align' => 'text-center'],
                ['label' => 'Emissão', 'icone' => '', 'align' => 'text-center'],
                ['label' => 'Tipo', 'icone' => '', 'align' => 'text-center'],
                ['label' => 'Saldo Empenhado', 'icone' => '', 'align' => 'text-end'],
                ['label' => 'Saldo a Pagar', 'icone' => '', 'align' => 'text-end'],
            ];
        @endphp

        <x-tabela-transparencia titulo="Empenhos emitidos em {{ $exercicio }}" cor="primary" :colunas="$columns">
            @foreach ($empenhos as $e)
                <tr>
                    {{-- Botão de Lupa --}}
                    <td class="text-center">
                        <a href="{{ route('empenho.credor.empenho.detalhe', ['exercicio' => $exercicio, 'credor_id' => $e->credor_id, 'empenho_id' => $e->id]) }}"
                            class="btn btn-sm btn-outline-secondary" title="Ver detalhes">
                            <i class="fa fa-search"></i>
                        </a>
                    </td>

                    <td class="text-center">{{ $e->numero }}</td>
                    <td class="text-center">{{ date('d/m/Y', strtotime($e->data_emissao)) }}</td>
                    <td class="text-center">
                        @if ($e->tipo == 'O')
                            <span
                                class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 rounded-pill px-3"
                                style="font-size: 0.75rem;">
                                ORÇAMENTÁRIO
                            </span>
                        @else
                            <span
                                class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-3"
                                style="font-size: 0.75rem;">
                                RESTOS A PAGAR
                            </span>
                        @endif
                    </td>
                    <td class="text-end">R$ {{ number_format($e->saldo_empenhado, 2, ',', '.') }}</td>
                    <td class="text-end fw-bold">R$ {{ number_format($e->saldo_pagar, 2, ',', '.') }}</td>
                </tr>
            @endforeach

            <tfoot class="table-light fw-bold">
                <tr class="text-nowrap">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="text-end">TOTAIS:</td>
                    <td class="text-end">
                        R$ {{ number_format($empenhos->sum('saldo_empenhado'), 2, ',', '.') }}
                    </td>
                    <td class="text-end">
                        R$ {{ number_format($empenhos->sum('saldo_pagar'), 2, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        </x-tabela-transparencia>
        @include('layouts.partials.back')

    </div>

    <style>
        .nav-tabs .nav-link.active {
            background-color: #fff !important;
            color: var(--bs-primary) !important;
            border-bottom: none;
        }

        .nav-tabs .nav-link:hover {
            border-color: transparent;
            opacity: 0.8;
        }
    </style>
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
