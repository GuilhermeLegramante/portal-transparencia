@extends('layouts.app')

@section('content')
    <div class="container">
        {{-- Breadcrumb conforme o padrão --}}
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb bg-light p-2 rounded shadow-sm small">
                <li class="breadcrumb-item">
                    <a href="/" class="text-decoration-none text-muted">
                        Despesa
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('despesa.diarias.resumo') }}" class="text-decoration-none text-muted">Diárias</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('despesa.diarias.detalhe', ['exercicio' => $exc]) }}"
                        class="text-decoration-none text-muted">Exercício {{ $exc }}</a>
                </li>
                <li class="breadcrumb-item active text-danger fw-bold" aria-current="page">
                    <i class="fa fa-user me-1"></i>{{ $credor->nome }}
                </li>
            </ol>
        </nav>

        {{-- Bloco Azul: Dados do Credor --}}
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

        @php
            $columns = [
                ['label' => '', 'icone' => 'fa-search', 'align' => 'text-center'],
                ['label' => 'Número', 'icone' => '', 'align' => 'text-center'],
                ['label' => 'Emissão', 'icone' => '', 'align' => 'text-center'],
                ['label' => 'Tipo', 'icone' => '', 'align' => 'text-center'],
                ['label' => 'Saldo empenhado', 'icone' => '', 'align' => 'text-end'],
                ['label' => 'Saldo liquidar', 'icone' => '', 'align' => 'text-end'],
                ['label' => 'Saldo pagar', 'icone' => '', 'align' => 'text-end'],
            ];
        @endphp

        {{-- Componente Tabela (Bloco Verde conforme imagem) --}}
        <x-tabela-transparencia titulo="Empenhos emitidos para o exercício {{ $exc }}" cor="primary"
            :colunas="$columns">
            @foreach ($empenhos as $item)
                <tr>
                    <td class="text-center">
                        <a href="{{ route('despesa.diarias.empenho', ['exc' => $exc, 'cad' => $credor->id, 'emp' => $item->empenho_id]) }}"
                            class="btn btn-sm btn-outline-secondary" title="Ver detalhes do empenho {{ $item->numero }}">
                            <i class="fa fa-search"></i>
                        </a>
                    </td>
                    <td class="text-center align-middle">{{ $item->numero }}</td>
                    <td class="text-center align-middle">{{ \Carbon\Carbon::parse($item->data_emissao)->format('d/m/Y') }}
                    </td>
                    <td class="text-center align-middle">
                        @if ($item->tipo == 'O')
                            <span
                                class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 rounded-pill px-3"
                                style="font-size: 0.75rem;">
                                ORÇAMENTÁRIO
                            </span>
                        @elseif($item->tipo == 'R')
                            <span
                                class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-3"
                                style="font-size: 0.75rem;">
                                RESTOS A PAGAR
                            </span>
                        @else
                            <span
                                class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 rounded-pill px-3"
                                style="font-size: 0.75rem;">
                                {{ $item->tipo }}
                            </span>
                        @endif
                    </td>
                    <td class="text-end align-middle">{{ number_format($item->saldo_empenhado, 2, ',', '.') }}</td>
                    <td class="text-end align-middle">{{ number_format($item->saldo_liquidar, 2, ',', '.') }}</td>
                    <td class="text-end fw-bold align-middle">{{ number_format($item->saldo_pagar, 2, ',', '.') }}</td>
                </tr>
            @endforeach

            <tfoot class="table-light fw-bold border-top-2">
                <tr>
                    <td colspan="4" class="text-end">TOTAIS:</td>
                    <td class="text-end">{{ number_format($empenhos->sum('saldo_empenhado'), 2, ',', '.') }}</td>
                    <td class="text-end">{{ number_format($empenhos->sum('saldo_liquidar'), 2, ',', '.') }}</td>
                    <td class="text-end text-success">{{ number_format($empenhos->sum('saldo_pagar'), 2, ',', '.') }}</td>
                </tr>
            </tfoot>
        </x-tabela-transparencia>

        <div class="mt-4">
            <a href="{{ route('despesa.diarias.detalhe', ['exercicio' => $exc]) }}" class="btn btn-secondary shadow-sm">
                <i class="fa fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
@endsection
