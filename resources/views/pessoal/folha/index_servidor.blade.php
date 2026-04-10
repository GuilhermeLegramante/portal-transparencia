@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <x-breadcrumb :items="$breadcrumb" />
        
        {{-- Cards de Resumo (Reutilizando seu padrão) --}}
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

        {{-- Filtros Avançados --}}
        <form class="card p-3 mb-4 shadow-sm border-0 bg-light" method="GET">
            <div class="row g-3">
                <div class="col-md-2">
                    <label class="small fw-bold">Exercício</label>
                    <input type="number" name="exercicio" value="{{ $exercicio }}" class="form-control">
                </div>
                <div class="col-md-2">
                    <label class="small fw-bold">Mês</label>
                    <select name="mes" class="form-select">
                        @foreach (range(1, 12) as $m)
                            <option value="{{ $m }}" {{ $mes == $m ? 'selected' : '' }}>{{ $m }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5">
                    <label class="small fw-bold">Buscar Servidor (Nome, CPF ou Matrícula)</label>
                    <input type="text" name="busca" value="{{ $busca }}" class="form-control"
                        placeholder="Digite para filtrar...">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button class="btn btn-primary w-100"><i class="fa fa-search me-2"></i>Filtrar</button>
                </div>
            </div>
        </form>

        <x-tabela-transparencia titulo="Folha por Servidor" :colunas="[
            ['label' => 'Matrícula', 'align' => 'text-center'],
            ['label' => 'Servidor', 'align' => 'text-start'],
            ['label' => 'Proventos', 'align' => 'text-end'],
            ['label' => 'Descontos', 'align' => 'text-end'],
            ['label' => 'Líquido', 'align' => 'text-end'],
            ['label' => 'Ação', 'align' => 'text-center'],
        ]">
            @foreach ($dados as $d)
                <tr>
                    <td class="text-center font-monospace">{{ $d->matricula }}</td>
                    <td>
                        <span class="fw-bold d-block">{{ $d->nome }}</span>
                        <small class="text-muted">CPF: {{ $d->cpf }}</small>
                    </td>
                    <td class="text-end text-success">R$ {{ number_format($d->total_provento, 2, ',', '.') }}</td>
                    <td class="text-end text-danger">R$ {{ number_format($d->total_desconto, 2, ',', '.') }}</td>
                    <td class="text-end fw-bold">R$
                        {{ number_format($d->total_provento - $d->total_desconto, 2, ',', '.') }}</td>
                    <td class="text-center">
                        <a href="{{ route('pessoal.folha.contracheque', [$exercicio, $mes, $d->contrato_id]) }}"
                            class="btn btn-sm btn-primary">
                            <i class="fa fa-file-invoice-dollar me-1"></i> Contracheque
                        </a>
                    </td>
                </tr>
            @endforeach
        </x-tabela-transparencia>

        {{-- Paginação --}}
        <div class="mt-3">
            {{ $dados->appends(request()->all())->links() }}
        </div>
    </div>
@endsection
