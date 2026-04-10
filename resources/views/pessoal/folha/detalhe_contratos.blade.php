@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <x-breadcrumb :items="$breadcrumb" />

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body bg-light d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0 text-dark">
                        <i class="fa fa-users text-primary me-2"></i>
                        Período: <span class="fw-bold">{{ str_pad($mes, 2, '0', STR_PAD_LEFT) }}/{{ $exercicio }}</span>
                    </h5>
                    <small class="text-muted text-uppercase">Listagem de contratos calculados para este grupo</small>
                </div>
                <a href="{{ route('pessoal.folha.' . $tipo, ['exercicio' => $exercicio, 'mes' => $mes]) }}"
                    class="btn btn-outline-secondary btn-sm">
                    <i class="fa fa-arrow-left me-1"></i> Voltar
                </a>
            </div>
        </div>

        <x-tabela-transparencia :titulo="'Contratos Calculados'" cor="info" :colunas="[
            ['label' => 'Inscrição', 'align' => 'text-center'],
            ['label' => 'Matrícula', 'align' => 'text-center'],
            ['label' => 'Nome do Servidor', 'align' => 'text-start'],
            ['label' => 'Admissão', 'align' => 'text-center'],
            ['label' => 'Proventos', 'align' => 'text-end'],
            ['label' => 'Descontos', 'align' => 'text-end'],
            ['label' => 'Líquido', 'align' => 'text-end'],
            ['label' => 'Ação', 'align' => 'text-center'],
        ]">
            @foreach ($contratos as $c)
                <tr>
                    <td class="text-center text-muted">{{ $c->inscricao }}</td>
                    <td class="text-center fw-bold">{{ $c->matricula }}</td>
                    <td>{{ $c->nome }}</td>
                    <td class="text-center">{{ date('d/m/Y', strtotime($c->admissao)) }}</td>
                    <td class="text-end">R$ {{ number_format($c->total_provento, 2, ',', '.') }}</td>
                    <td class="text-end text-danger">R$ {{ number_format($c->total_desconto, 2, ',', '.') }}</td>
                    <td class="text-end fw-bold text-success">
                        R$ {{ number_format($c->total_provento - $c->total_desconto, 2, ',', '.') }}
                    </td>
                    <td class="text-center">
                        <a href="{{ route('pessoal.folha.contracheque', [$exercicio, $mes, $c->id]) }}"
                            class="btn btn-sm btn-primary shadow-sm" title="Visualizar Contracheque">
                            <i class="fa fa-file-invoice-dollar"></i> Contracheque
                        </a>
                    </td>
                </tr>
            @endforeach
        </x-tabela-transparencia>

        {{-- Resumo do Rodapé da Categoria --}}
        <div class="row mt-3">
            <div class="col-md-12">
                <div class="alert alert-secondary border-0 shadow-sm d-flex justify-content-end align-items-center">
                    <span class="me-4">Total de Contratos neste grupo: <strong>{{ count($contratos) }}</strong></span>
                    <span class="me-4">Total Bruto: <strong>R$
                            {{ number_format($contratos->sum('total_provento'), 2, ',', '.') }}</strong></span>
                    <span>Total Líquido: <strong class="text-dark">R$
                            {{ number_format($contratos->sum('total_provento') - $contratos->sum('total_desconto'), 2, ',', '.') }}</strong></span>
                </div>
            </div>
        </div>
        @include('layouts.partials.back')
    </div>
@endsection
