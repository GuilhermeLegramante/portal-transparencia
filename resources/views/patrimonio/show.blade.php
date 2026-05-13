@extends('layouts.app')
@section('content')
    <style>
        .info-label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #64748b;
            font-weight: 700;
            margin-bottom: 2px;
        }

        .info-value {
            font-size: 1rem;
            color: #1e293b;
            font-weight: 600;
        }

        .card-custom {
            border-radius: 15px;
            transition: transform 0.2s;
        }

        .icon-shape {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            margin-right: 15px;
        }

        .bg-soft-primary {
            background-color: #e0e7ff;
            color: #4338ca;
        }

        .bg-soft-danger {
            background-color: #fee2e2;
            color: #b91c1c;
        }

        .bg-soft-success {
            background-color: #dcfce7;
            color: #15803d;
        }

        .bg-soft-info {
            background-color: #e0f2fe;
            color: #0369a1;
        }

        .divider-vertical {
            border-left: 1px solid #e2e8f0;
            height: 100%;
            margin: 0 15px;
        }
    </style>

    <div class="container-fluid pb-5">
        <x-breadcrumb :items="$breadcrumb" />

        {{-- 1. CABEÇALHO PRINCIPAL --}}
        <div class="card card-custom shadow-sm border-0 mb-4 overflow-hidden">
            <div class="card-header bg-white py-3 border-0">
                <div class="d-flex align-items-center">
                    <div class="icon-shape bg-soft-primary">
                        <i class="fa-solid fa-box-archive fa-lg"></i>
                    </div>
                    <div>
                        <h4 class="mb-0 fw-bold text-dark">{{ $patrimonio->nome_produto }}</h4>
                        <span class="badge bg-soft-primary px-3 py-2">Patrimônio Nº {{ $patrimonio->numero }}</span>
                    </div>
                </div>
            </div>
            <div class="card-body bg-light/30">
                <div class="row g-4">
                    <div class="col-md-12">
                        <p class="text-secondary mb-0"><i
                                class="fa-solid fa-align-left me-2"></i>{{ $patrimonio->complemento ?: 'Sem descrição complementar.' }}
                        </p>
                    </div>
                    <hr class="my-2 opacity-50">

                    @php
                        $dadosPrincipais = [
                            [
                                'label' => 'Data Compra',
                                'val' => date('d/m/Y', strtotime($patrimonio->datacompra)),
                                'icon' => 'calendar-check',
                            ],
                            [
                                'label' => 'Início Uso',
                                'val' => date('d/m/Y', strtotime($patrimonio->datainicio)),
                                'icon' => 'play',
                            ],
                            ['label' => 'Classificação', 'val' => $patrimonio->classificacao, 'icon' => 'sitemap'],
                            ['label' => 'Espécie', 'val' => $patrimonio->especie, 'icon' => 'tag'],
                            ['label' => 'Classe', 'val' => $patrimonio->classe, 'icon' => 'layer-group'],
                            ['label' => 'Conservação', 'val' => $patrimonio->conservacao, 'icon' => 'shield-heart'],
                            [
                                'label' => 'Valor de Entrada',
                                'val' => 'R$ ' . number_format($patrimonio->valorentrada, 2, ',', '.'),
                                'icon' => 'money-bill-wave',
                            ],
                            [
                                'label' => 'Situação',
                                'val' =>
                                    $patrimonio->situacao == 'LIB'
                                        ? 'LIBERADO'
                                        : ($patrimonio->situacao == 'BAI'
                                            ? 'BAIXADO'
                                            : $patrimonio->situacao),
                                'icon' => 'circle-info',
                            ],
                        ];
                    @endphp

                    @foreach ($dadosPrincipais as $item)
                        <div class="col-md-3">
                            <div class="d-flex align-items-center">
                                <i class="fa-solid fa-{{ $item['icon'] }} text-muted me-3" style="width: 20px;"></i>
                                <div>
                                    <div class="info-label">{{ $item['label'] }}</div>
                                    <div class="info-value">{{ $item['val'] }}</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="row">
            {{-- 2. MOVIMENTAÇÕES --}}
            <div class="{{ $baixa || $veiculo || $semovente ? 'col-lg-8' : 'col-12' }}">
                <div class="card card-custom shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold"><i class="fa-solid fa-route text-primary me-2"></i>Histórico de
                            Movimentações</h5>
                    </div>
                    <div class="card-body p-0">
                        @if ($movimentacoes->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-4 border-0 text-muted small uppercase">Código</th>
                                            <th class="border-0 text-muted small uppercase">Descrição da Movimentação</th>
                                            <th class="text-end pe-4 border-0 text-muted small uppercase">Valor Atualizado
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($movimentacoes as $mov)
                                            <tr>
                                                <td class="ps-4 fw-bold text-primary">{{ $mov->codigo }}</td>
                                                <td>{{ $mov->descricao }}</td>
                                                <td class="text-end pe-4 fw-bold text-dark">R$
                                                    {{ number_format($mov->valor, 2, ',', '.') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="p-5 text-center">
                                <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" width="60"
                                    class="opacity-25 mb-3">
                                <p class="text-muted">Nenhuma movimentação registrada para este bem.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- 3. COLUNA LATERAL (BAIXA, VEÍCULO, SEMOVENTE) --}}
            <div class="col-lg-4">
                @if ($baixa)
                    <div class="card card-custom shadow-sm border-0 mb-4 border-top border-4 border-danger">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="icon-shape bg-soft-danger me-2" style="width:35px; height:35px;">
                                    <i class="fa-solid fa-circle-down"></i>
                                </div>
                                <h6 class="mb-0 fw-bold text-danger text-uppercase">Dados da Baixa</h6>
                            </div>
                            <div class="bg-light p-3 rounded-3">
                                <div class="mb-2">
                                    <div class="info-label">Termo / Número</div>
                                    <div class="info-value">{{ $baixa->termo }} / {{ $baixa->numero }}</div>
                                </div>
                                <div class="mb-2">
                                    <div class="info-label">Data do Evento</div>
                                    <div class="info-value text-danger">{{ date('d/m/Y', strtotime($baixa->data)) }}</div>
                                </div>
                                <div class="mb-2">
                                    <div class="info-label">Tipo de Operação</div>
                                    <div class="badge bg-danger">
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
                                    </div>
                                </div>
                                <div>
                                    <div class="info-label">Destinação</div>
                                    <div class="info-value">
                                        {{ match ($baixa->destinacao) {
                                            'DOA' => 'Doação',
                                            'ALI' => 'Alienação',
                                            'SUC' => 'Sucata',
                                            'OUT' => 'Outros',
                                            default => $baixa->destinacao,
                                        } }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($veiculo)
                    <div class="card card-custom shadow-sm border-0 mb-4 border-top border-4 border-info">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="icon-shape bg-soft-info me-2" style="width:35px; height:35px;">
                                    <i class="fa-solid fa-car"></i>
                                </div>
                                <h6 class="mb-0 fw-bold text-info text-uppercase">Especificações do Veículo</h6>
                            </div>
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="info-label">Marca</div>
                                    <div class="info-value small">{{ $veiculo->marca }}</div>
                                </div>
                                <div class="col-6">
                                    <div class="info-label">Modelo</div>
                                    <div class="info-value small">{{ $veiculo->modelo }}</div>
                                </div>
                                <div class="col-6">
                                    <div class="info-label">Placa</div>
                                    <div class="badge bg-dark font-monospace">{{ $veiculo->placa }}</div>
                                </div>
                                <div class="col-6">
                                    <div class="info-label">Ano</div>
                                    <div class="info-value small">{{ $veiculo->anofabricacao }}/{{ $veiculo->anomodelo }}
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="info-label">Combustível / Cor</div>
                                    <div class="info-value small">{{ $veiculo->combustivel }} | {{ $veiculo->cor }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($semovente)
                    <div class="card card-custom shadow-sm border-0 mb-4 border-top border-4 border-success">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="icon-shape bg-soft-success me-2" style="width:35px; height:35px;">
                                    <i class="fa-solid fa-cow"></i>
                                </div>
                                <h6 class="mb-0 fw-bold text-success text-uppercase">Dados do Semovente</h6>
                            </div>
                            <div class="bg-soft-success p-3 rounded-3 mb-2">
                                <div class="info-label text-success">Registro / Brinco</div>
                                <div class="h5 fw-bold mb-0">{{ $semovente->registro }} / {{ $semovente->brinco }}</div>
                            </div>
                            <div class="info-label">Espécie</div>
                            <div class="info-value mb-2">{{ $semovente->especie }}</div>
                            <div class="info-label">Sexo</div>
                            <div class="info-value">
                                <i
                                    class="fa-solid fa-{{ $semovente->sexo == 'M' ? 'mars text-primary' : 'venus text-danger' }} me-1"></i>
                                {{ $semovente->sexo == 'M' ? 'Macho' : 'Fêmea' }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="mt-4">
            @include('layouts.partials.back')
        </div>
    </div>
@endsection
