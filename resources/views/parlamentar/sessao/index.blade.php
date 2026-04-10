@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <x-breadcrumb :items="['Parlamentar' => '#', 'Sessões' => '']" />

        <div class="card shadow-sm border-0 mb-4 bg-light">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-2">
                        <label class="small fw-bold">Exercício</label>
                        <input type="number" name="exercicio" class="form-control" value="{{ $exercicio }}">
                    </div>
                    <div class="col-md-4">
                        <label class="small fw-bold">Legislatura</label>
                        <select name="legislatura" class="form-select">
                            <option value="">Todas</option>
                            @foreach ($legislaturas as $l)
                                <option value="{{ $l->id }}"
                                    {{ request('legislatura') == $l->id ? 'selected' : '' }}>{{ $l->descricao }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button class="btn btn-primary w-100"><i class="fa fa-search me-2"></i>Filtrar</button>
                    </div>
                </form>
            </div>
        </div>

        <x-tabela-transparencia titulo="Relação de Sessões" :colunas="[
            ['label' => 'Nº', 'align' => 'text-center'],
            ['label' => 'Descrição', 'align' => 'text-start'],
            ['label' => 'Data', 'align' => 'text-center'],
            ['label' => 'Tipo', 'align' => 'text-center'],
            ['label' => 'Situação', 'align' => 'text-center'],
            ['label' => 'Ações', 'align' => 'text-center'],
        ]">
            @foreach ($sessoes as $s)
                <tr>
                    <td class="text-center font-monospace">{{ $s->numero }}</td>
                    <td>{{ $s->descricao }}</td>
                    <td class="text-center">{{ date('d/m/Y H:i', strtotime($s->horario)) }}</td>
                    <td class="text-center small text-uppercase">{{ $s->tipo }}</td>
                    <td class="text-center">
                        <span class="badge bg-info">{{ $s->situacao }}</span>
                    </td>
                    <td class="text-center">
                        <a href="{{ route('parlamentar.sessao.show', $s->id) }}" class="btn btn-sm btn-outline-primary">
                            <i class="fa fa-eye"></i> Detalhar
                        </a>
                    </td>
                </tr>
            @endforeach
        </x-tabela-transparencia>
    </div>
@endsection
