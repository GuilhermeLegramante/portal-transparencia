@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <x-breadcrumb :items="$breadcrumb" />
        
        {{-- Cards de Resumo de Exercícios --}}
        <div class="row mb-4">
            @foreach ($resumo->take(4) as $r)
                <div class="col-md-3">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h6 class="text-muted">{{ $r->exercicio }}</h6>
                            <div class="d-flex justify-content-between">
                                <span>Líquido:</span>
                                <span class="fw-bold text-success">R$
                                    {{ number_format($r->valor_liquido, 2, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Filtros --}}
        <form class="card p-3 mb-4 shadow-sm border-0 bg-light">
            <div class="row g-3">
                <div class="col-md-3">
                    <label>Exercício</label>
                    <input type="number" name="exercicio" value="{{ $exercicio }}" class="form-control">
                </div>
                <div class="col-md-3">
                    <label>Mês</label>
                    <select name="mes" class="form-select">
                        @foreach (range(1, 12) as $m)
                            <option value="{{ $m }}" {{ $mes == $m ? 'selected' : '' }}>{{ $m }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button class="btn btn-primary w-100">Filtrar</button>
                </div>
            </div>
        </form>

        <x-tabela-transparencia :titulo="'Folha ' . $titulo" cor="primary" :colunas="[
            ['label' => 'Código', 'align' => 'text-center'],
            ['label' => 'Descrição', 'align' => 'text-start'],
            ['label' => 'Qtd', 'align' => 'text-center'],
            ['label' => 'Proventos', 'align' => 'text-end'],
            ['label' => 'Descontos', 'align' => 'text-end'],
            ['label' => 'Líquido', 'align' => 'text-end'],
            ['label' => 'Ação', 'align' => 'text-center'],
        ]">
            @foreach ($dados as $d)
                <tr>
                    <td class="text-center">{{ $d->codigo }}</td>
                    <td>
                        {{-- Se for lotação e tiver unidade, exibe como um subtexto --}}
                        @if (isset($d->unidade_nome))
                            <small class="text-muted d-block" style="font-size: 0.7rem;">{{ $d->unidade_nome }}</small>
                        @endif
                        <span class="fw-bold">{{ $d->descricao }}</span>
                    </td>
                    <td class="text-center">{{ $d->quantidade }}</td>
                    <td class="text-end">R$ {{ number_format($d->total_provento, 2, ',', '.') }}</td>
                    <td class="text-end text-danger">R$ {{ number_format($d->total_desconto, 2, ',', '.') }}</td>
                    <td class="text-end fw-bold">
                        R$ {{ number_format($d->total_provento - $d->total_desconto, 2, ',', '.') }}
                    </td>
                    <td class="text-center">
                        @php
                            // Identifica o ID correto para o detalhamento baseado no tipo
                            $idFiltro = $d->funcao_id ?? ($d->lotacao_id ?? $d->regime_id);
                        @endphp

                        <a href="{{ route('pessoal.folha.contratos', [$tipo, $exercicio, $mes, $idFiltro]) }}"
                            class="btn btn-sm btn-primary text-white shadow-sm">
                            <i class="fa fa-search me-1"></i> Detalhar
                        </a>
                    </td>
                </tr>
            @endforeach
        </x-tabela-transparencia>
        @include('layouts.partials.back')
    </div>
@endsection
