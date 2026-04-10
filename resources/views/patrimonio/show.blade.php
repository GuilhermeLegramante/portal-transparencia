@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <x-breadcrumb :items="$breadcrumb" />

        {{-- 1. Cabeçalho de Dados Patrimoniais --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-primary text-white">Dados do Bem Patrimonial: {{ $patrimonio->numero }}</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-8">
                        <h4 class="fw-bold">{{ $patrimonio->nome_produto }}</h4>
                        <p class="text-muted">{{ $patrimonio->complemento }}</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <span class="badge bg-dark p-2">Classe: {{ $patrimonio->classe }}</span>
                    </div>
                    <hr>
                    @php
                        $campos = [
                            'Nº Patrimônio' => $patrimonio->numero,
                            'Data Compra' => date('d/m/Y', strtotime($patrimonio->datacompra)),
                            'Início Uso' => date('d/m/Y', strtotime($patrimonio->datainicio)),
                            'Classificação' => $patrimonio->classificacao,
                            'Espécie' => $patrimonio->especie,
                            // 'Local' => $patrimonio->local,
                            'Conservação' => $patrimonio->conservacao,
                            'Valor' => 'R$ ' . number_format($patrimonio->valorentrada, 2, ',', '.'),
                            'Situação' =>
                                $patrimonio->situacao == 'LIB'
                                    ? 'LIBERADO'
                                    : ($patrimonio->situacao == 'BAI'
                                        ? 'BAIXADO'
                                        : $patrimonio->situacao),
                        ];
                    @endphp
                    @foreach ($campos as $label => $val)
                        <div class="col-md-3">
                            <label class="small text-muted d-block">{{ $label }}</label>
                            <span class="fw-bold">{{ $val }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="row">
            {{-- Movimentações --}}
            {{-- <div class="col-md-6 mb-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header">Movimentações</div>
                    <div class="card-body p-0">
                        <table class="table table-sm mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Código</th>
                                    <th>Descrição</th>
                                    <th class="text-end">Valor</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($movimentacoes as $m)
                                    <tr>
                                        <td>{{ $m->codigo }}</td>
                                        <td>{{ $m->descricao }}</td>
                                        <td class="text-end">R$ {{ number_format($m->valor, 2, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center p-3 text-muted">Sem movimentações</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div> --}}

            {{-- Dados Específicos (Veículo / Baixa / Semovente) --}}
            <div class="col-md-6 mb-4">
                {{-- Veículo --}}
                {{-- @if ($veiculo)
                    <div class="card shadow-sm border-0 mb-3 border-start border-4 border-info">
                        <div class="card-header fw-bold">Dados do Veículo</div>
                        <div class="card-body py-2">
                            <p class="mb-1 small"><strong>Marca/Modelo:</strong> {{ $veiculo->marca }} /
                                {{ $veiculo->modelo }} ({{ $veiculo->ano }})</p>
                            <p class="mb-1 small"><strong>Placa:</strong> {{ $veiculo->placa }} |
                                <strong>Combustível:</strong> {{ $veiculo->combustivel }}</p>
                        </div>
                    </div>
                @endif --}}

                {{-- Baixa --}}
                {{-- @if ($baixa)
                    <div class="card shadow-sm border-0 mb-3 border-start border-4 border-danger">
                        <div class="card-header fw-bold">Dados da Baixa</div>
                        <div class="card-body py-2 small">
                            <strong>Termo:</strong> {{ $baixa->termo }} ({{ $baixa->numero }}) | <strong>Data:</strong>
                            {{ date('d/m/Y', strtotime($baixa->data)) }}<br>
                            <strong>Destinação:</strong> {{ $baixa->destination }}
                        </div>
                    </div>
                @endif --}}
            </div>
        </div>
        @include('layouts.partials.back')
    </div>
@endsection
