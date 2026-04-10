@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <x-breadcrumb :items="$breadcrumb" />

        <form class="card p-3 mb-4 shadow-sm border-0 bg-light" method="GET">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="small fw-bold">Classe</label>
                    <select name="classe" class="form-select">
                        <option value="">Todas</option>
                        @foreach ($opcoesFiltros['classes'] as $c)
                            <option value="{{ $c }}" {{ request('classe') == $c ? 'selected' : '' }}>
                                {{ $c }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="small fw-bold">Espécie</label>
                    <select name="especie" class="form-select">
                        <option value="">Todas</option>
                        @foreach ($opcoesFiltros['especies'] as $e)
                            <option value="{{ $e }}" {{ request('especie') == $e ? 'selected' : '' }}>
                                {{ $e }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="small fw-bold">Classificação</label>
                    <select name="classificacao" class="form-select">
                        <option value="">Todas</option>
                        @foreach ($opcoesFiltros['classificacoes'] as $cl)
                            <option value="{{ $cl }}" {{ request('classificacao') == $cl ? 'selected' : '' }}>
                                {{ $cl }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button class="btn btn-primary w-100"><i class="fa fa-search me-2"></i>Filtrar</button>
                </div>
            </div>
        </form>

        <x-tabela-transparencia titulo="Bens Patrimoniais" :colunas="[
            ['label' => 'Nº', 'align' => 'text-center'],
            ['label' => 'Descrição', 'align' => 'text-start'],
            ['label' => 'Início Uso', 'align' => 'text-center'],
            ['label' => 'Valor Compra', 'align' => 'text-end'],
            ['label' => 'Situação', 'align' => 'text-center'],
            ['label' => 'Ações', 'align' => 'text-center'],
        ]">
            @foreach ($patrimonios as $p)
                <tr>
                    <td class="text-center fw-bold">{{ $p->numero }}</td>
                    <td>{{ $p->descricao }}</td>
                    <td class="text-center">{{ date('d/m/Y', strtotime($p->data_inicio)) }}</td>
                    <td class="text-end">R$ {{ number_format($p->valor_compra, 2, ',', '.') }}</td>
                    <td class="text-center">
                        <span class="badge {{ $p->situacao == 'LIB' ? 'bg-success' : 'bg-warning text-dark' }}">
                            @if ($p->situacao == 'LIB')
                                LIBERADO
                            @elseif($p->situacao == 'BAI')
                                BAIXADO
                            @endif
                        </span>
                    </td>
                    <td class="text-center">
                        <a href="{{ route('patrimonio.show', $p->patrimonio_id) }}" class="btn btn-sm btn-primary">
                            <i class="fa fa-search"></i>
                        </a>
                    </td>
                </tr>
            @endforeach
        </x-tabela-transparencia>
    </div>
@endsection
