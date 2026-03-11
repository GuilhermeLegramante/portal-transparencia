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
        {{-- Breadcrumb aqui --}}
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb bg-light p-2 rounded shadow-sm small">
                <li class="breadcrumb-item"><a href="/" class="text-decoration-none text-muted">Despesa</a></li>
                <li class="breadcrumb-item"><a href="{{ route('despesa.diarias.resumo') }}"
                        class="text-decoration-none text-muted">Diárias</a></li>
                <li class="breadcrumb-item"><a href="{{ route('despesa.diarias.detalhe', ['exercicio' => $exc]) }}"
                        class="text-decoration-none text-muted">Exercício {{ $exc }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('despesa.diarias.credor', ['exc' => $exc, 'cad' => $cad]) }}"
                        class="text-decoration-none text-muted">{{ $empenho->nome_municipe }}</a></li>
                <li class="breadcrumb-item active text-danger fw-bold" aria-current="page">Empenho nº {{ $empenho->numero }}
                </li>
            </ol>
        </nav>

        <div class="card mb-4 border-primary shadow-sm">
            <div class="card-header bg-primary text-white ">
                <i class="fa fa-user-circle me-2"></i> Dados do credor
            </div>
            <div class="card-body p-0">
                <table class="table table-bordered mb-0">
                    <tr>
                        <th width="200" class="bg-light text-muted">Inscrição:</th>
                        <td>{{ $credor->id }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light text-muted">Nome:</th>
                        <td class="fw-bold">{{ $credor->nome }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light text-muted">CPF/CNPJ:</th>
                        <td>***.***.***-**</td>
                    </tr>
                    <tr>
                        <th class="bg-light text-muted">Tipo pessoa:</th>
                        <td>
                            <span class="badge bg-info rounded-pill">
                                {{ $credor->tipopessoa == 'F' ? 'Física' : 'Jurídica' }}
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- 1. IDENTIFICAÇÃO E DOTAÇÃO (Abas conforme imagem) --}}
        <div class="card mb-4 border-primary shadow-sm">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center py-2">
                <span class="fw-bold"><i class="fa fa-info-circle me-2"></i> Dados do empenho</span>

                <ul class="nav nav-tabs card-header-tabs border-bottom-0" id="myTab" role="tablist">
                    {{-- Aba Identificação --}}
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-bold text-uppercase small px-3 border-0" id="ident-tab"
                            data-bs-toggle="tab" data-bs-target="#ident" type="button" role="tab">
                            Identificação
                        </button>
                    </li>

                    {{-- Aba Dotação --}}
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold text-uppercase small px-3 border-0 text-white" id="dotac-tab"
                            data-bs-toggle="tab" data-bs-target="#dotac" type="button" role="tab">
                            Dotação
                        </button>
                    </li>
                </ul>
            </div>

            <div class="card-body p-0 tab-content">
                {{-- TAB IDENTIFICAÇÃO --}}
                <div class="tab-pane fade show active" id="ident">
                    <table class="table table-bordered mb-0 small">
                        <tr>
                            <th width="200" class="bg-light text-muted">Número / Exercício:</th>
                            <td class="fw-bold">{{ $empenho->numero }} / {{ $exc }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light text-muted">Data de Emissão:</th>
                            <td>{{ date('d/m/Y', strtotime($empenho->dataemissao)) }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light text-muted">Credor / CPF-CNPJ:</th>
                            <td>
                                <span class="fw-bold text-primary">{{ $empenho->nome_municipe }}</span>
                                <span class="ms-2 text-muted">({{ $empenho->documento }})</span>
                            </td>
                        </tr>
                        <tr>
                            <th class="bg-light text-muted">Modalidade / Espécie:</th>
                            <td>{{ $empenho->modalidade }} / {{ $empenho->especie }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light text-muted">Tipo:</th>
                            <td>
                                @if ($empenho->tipo == 'O')
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
                        </tr>
                        <tr>
                            <th class="bg-light text-muted">Objeto / Finalidade:</th>
                            <td>{{ $empenho->objeto }}</td>
                        </tr>
                    </table>
                </div>

                {{-- TAB DOTAÇÃO --}}
                <div class="tab-pane fade" id="dotac">
                    <table class="table table-bordered mb-0 small">
                        <tr>
                            <th width="200" class="bg-light text-muted">Órgão / Unidade:</th>
                            <td>{{ $empenho->orgao }} - {{ $empenho->unidade }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light text-muted">Funcional Programática:</th>
                            <td>{{ $empenho->funcao }} . {{ $empenho->sub_funcao }} . {{ $empenho->programa }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light text-muted">Elemento de Despesa:</th>
                            <td class="text-primary fw-bold">{{ $empenho->elemento }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light text-muted">Fonte de Recurso:</th>
                            <td>{{ $empenho->vinculo }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        {{-- 2. ITENS DO EMPENHO --}}
        @php
            $columns = [
                ['label' => 'Número', 'icone' => '', 'align' => 'text-center'],
                ['label' => 'Descrição', 'icone' => '', 'align' => 'text-start'],
                ['label' => 'Quantidade', 'icone' => '', 'align' => 'text-center'],
                ['label' => 'Valor unitário', 'icone' => '', 'align' => 'text-end'],
                ['label' => 'Total', 'icone' => '', 'align' => 'text-end'],
            ];
        @endphp

        <x-tabela-transparencia titulo="Itens do empenho" cor="primary" :colunas="$columns">
            @foreach ($itens as $it)
                <tr>
                    <td class="text-center">{{ $it->numero }}</td>
                    <td class="text-start small">{{ $it->descricao }}</td>
                    <td class="text-center">{{ number_format($it->quantidade, 2, ',', '.') }}</td>
                    <td class="text-end">{{ number_format($it->valor_unitario, 4, ',', '.') }}</td>
                    <td class="text-end fw-bold">{{ number_format($it->valor_total, 2, ',', '.') }}</td>
                </tr>
            @endforeach
            <tfoot class="table-light fw-bold">
                <tr>
                    <td colspan="4" class="text-end">TOTAL DOS ITENS:</td>
                    <td class="text-end text-primary">{{ number_format($itens->sum('valor_total'), 2, ',', '.') }}</td>
                </tr>
            </tfoot>
        </x-tabela-transparencia>

        <div class="mt-4">
            <a href="{{ route('despesa.diarias.credor', ['exc' => $exc, 'cad' => $cad]) }}"
                class="btn btn-secondary shadow-sm">
                <i class="fa fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
@endsection
