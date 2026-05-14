@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <x-breadcrumb :items="$breadcrumb" />

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white p-3">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h4 class="fw-bold mb-0 text-primary">
                            <i class="fas fa-list-ul me-2"></i>Relação Anual de Pedidos - {{ $exercicio }}
                        </h4>
                    </div>
                    <div class="col-md-6">
                        <form action="{{ route('sic.estatisticas') }}" method="GET"
                            class="d-flex justify-content-end gap-2">
                            <select name="exercicio" class="form-select w-auto">
                                @for ($i = date('Y'); $i >= 2020; $i--)
                                    <option value="{{ $i }}" {{ $exercicio == $i ? 'selected' : '' }}>Exercício
                                        {{ $i }}</option>
                                @endfor
                            </select>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i></button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped border datatable-transparencia">
                        <thead class="table-light">
                            <tr>
                                <th>Data/Hora</th>
                                <th>Solicitante</th>
                                <th>Tipo</th>
                                <th>Descrição</th>
                                <th>Situação</th>
                                <th>Avaliação</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pedidos as $p)
                                <tr>
                                    <td class="text-nowrap">{{ date('d/m/Y H:i', strtotime($p->datahora)) }}</td>
                                    <td class="fw-bold">{{ $p->solicitante }}</td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            {{ $p->tipopessoa == 'F' ? 'Física' : 'Jurídica' }}
                                        </span>
                                    </td>
                                    <td><small>{{ Str::limit($p->descricao, 100) }}</small></td>
                                    <td>
                                        @if ($p->situacao == 'Finalizado')
                                            <span class="badge bg-success"><i class="fas fa-check-circle"></i>
                                                Atendido</span>
                                        @else
                                            <span class="badge bg-warning text-dark"><i class="fas fa-clock"></i> Em
                                                Aberto</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($p->avaliacao)
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i
                                                    class="fas fa-star {{ $i <= $p->avaliacao ? 'text-warning' : 'text-muted' }}"></i>
                                            @endfor
                                        @else
                                            <span class="text-muted small">Não avaliado</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
