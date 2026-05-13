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

        {{-- 1. SEÇÃO DE MOVIMENTAÇÕES --}}
        @if ($movimentacoes->count() > 0)
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-light fw-bold"><i class="fa fa-history me-2"></i>Movimentações do Bem</div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-3">Código</th>
                                <th>Descrição</th>
                                <th class="text-end pe-3">Valor</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($movimentacoes as $mov)
                                <tr>
                                    <td class="ps-3">{{ $mov->codigo }}</td>
                                    <td>{{ $mov->descricao }}</td>
                                    <td class="text-end pe-3">R$ {{ number_format($mov->valor, 2, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <div class="row">
            {{-- 2. SEÇÃO DE BAIXA --}}
            @if ($baixa)
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 mb-4 border-start border-4 border-danger">
                        <div class="card-header bg-white fw-bold text-danger">Dados da Baixa</div>
                        <div class="card-body">
                            <p class="mb-1"><strong>Termo/Nº:</strong> {{ $baixa->termo }} / {{ $baixa->numero }}</p>
                            <p class="mb-1"><strong>Data:</strong> {{ date('d/m/Y', strtotime($baixa->data)) }}</p>
                            <p class="mb-1">
                                <strong>Operação:</strong>
                                {{ match ($baixa->tipooperacao) {
                                    'PER' => 'Perda',
                                    'DOA' => 'Doação',
                                    'DEV' => 'Devolução',
                                    'INC' => 'Incorporação',
                                    'INS' => 'Inservibilidade',
                                    'IMO' => 'Imóveis',
                                    'DEM' => 'Demais',
                                    default => $baixa->tipooperacao,
                                } }}
                            </p>

                            <p class="mb-1">
                                <strong>Destinação:</strong>
                                {{ match ($baixa->destinacao) {
                                    'DOA' => 'Doação',
                                    'ALI' => 'Alienação',
                                    'SUC' => 'Sucata',
                                    'OUT' => 'Outros',
                                    default => $baixa->destinacao,
                                } }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- 3. SEÇÃO DE VEÍCULO --}}
            @if ($veiculo)
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 mb-4 border-start border-4 border-primary">
                        <div class="card-header bg-white fw-bold text-primary">Informações do Veículo</div>
                        <div class="card-body small">
                            <div class="row">
                                <div class="col-6">
                                    <strong>Marca:</strong> {{ $veiculo->marca }}<br>
                                    <strong>Modelo:</strong> {{ $veiculo->modelo }}<br>
                                    <strong>Placa:</strong> <span class="badge bg-dark">{{ $veiculo->placa }}</span>
                                </div>
                                <div class="col-6">
                                    <strong>Ano:</strong> {{ $veiculo->anofabricacao }}/{{ $veiculo->anomodelo }}<br>
                                    <strong>Cor:</strong> {{ $veiculo->cor }}<br>
                                    <strong>Combustível:</strong> {{ $veiculo->combustivel }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- 4. SEÇÃO DE SEMOVENTE --}}
            @if ($semovente)
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 mb-4 border-start border-4 border-success">
                        <div class="card-header bg-white fw-bold text-success">Dados do Semovente</div>
                        <div class="card-body">
                            <p class="mb-1"><strong>Registro/Brinco:</strong> {{ $semovente->registro }} /
                                {{ $semovente->brinco }}</p>
                            <p class="mb-1"><strong>Espécie:</strong> {{ $semovente->especie }}</p>
                            <p class="mb-1"><strong>Sexo:</strong> {{ $semovente->sexo == 'M' ? 'Macho' : 'Fêmea' }}</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        @include('layouts.partials.back')
    </div>
@endsection
