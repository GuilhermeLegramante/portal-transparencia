@extends('layouts.app')
@section('content')
    <x-breadcrumb :items="['Compras' => '#', 'Contratos Administrativos' => '']" />

    <div class="container-fluid">
        @php
            $columns = [
                ['label' => 'Exercício', 'icone' => 'fa fa-calendar', 'align' => 'text-center'],
                ['label' => 'Ativos', 'icone' => '', 'align' => 'text-center'],
                ['label' => 'Encerrados', 'icone' => '', 'align' => 'text-center'],
                ['label' => 'Outros (Res/Sus/Anl)', 'icone' => '', 'align' => 'text-center'],
                ['label' => 'Total', 'icone' => '', 'align' => 'text-center'],
                ['label' => 'Ação', 'icone' => '', 'align' => 'text-center'],
            ];
        @endphp

        <x-tabela-transparencia titulo="Contratos por Ano de Início" cor="primary" :colunas="$columns">
            @foreach ($resumoAnual as $res)
                <tr>
                    <td class="text-center fw-bold">{{ $res->exercicio }}</td>
                    <td class="text-center"><span class="badge bg-success">{{ $res->ativo }}</span></td>
                    <td class="text-center"><span class="badge bg-secondary">{{ $res->encerrado }}</span></td>
                    <td class="text-center">
                        <span class="badge bg-danger" title="Rescindidos">{{ $res->rescindido }}</span>
                        <span class="badge bg-warning text-dark" title="Suspensos">{{ $res->suspenso }}</span>
                    </td>
                    <td class="text-center fw-bold">{{ $res->total }}</td>
                    <td class="text-center"> {{-- 7 --}}
                        <a href="{{ route('compras.licitacoes.contrato.list', $res->exercicio) }}"
                            class="btn btn-sm btn-primary">
                            <i class="fa fa-search"></i>
                        </a>
                    </td>
                </tr>
            @endforeach
        </x-tabela-transparencia>

        @include('layouts.partials.back')
    </div>
@endsection
