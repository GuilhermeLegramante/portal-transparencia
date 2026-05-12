@extends('layouts.app')
@section('content')
    <x-breadcrumb :items="['Compras' => '#', 'Registro de Preço' => route('compras.registro-preco.index'), 'Fornecedores' => '']" />

    <div class="container-fluid">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Cabeçalho do Registro: {{ $item->item_codigo }}</h5>
            </div>
            <div class="card-body bg-light">
                <h4 class="mb-0">{{ $item->descricao_item }}</h4>
            </div>
        </div>

        <x-tabela-transparencia titulo="Classificação de Fornecedores" cor="info" :colunas="[
            ['label' => 'Class.', 'icone' => 'fa fa-trophy', 'align' => 'text-center'],
            ['label' => 'Fornecedor', 'icone' => '', 'align' => 'text-start'],
            ['label' => 'Valor Unitário', 'icone' => '', 'align' => 'text-end'],
            ['label' => 'Status', 'icone' => '', 'align' => 'text-center'],
        ]">
            @foreach ($fornecedores as $f)
                <tr>
                    <td class="text-center">
                        @if ($f->classificacao == 1)
                            <span class="badge bg-warning text-dark"><i class="fa fa-medal"></i> 1º Lugar</span>
                        @else
                            <span class="fw-bold">{{ $f->classificacao }}º</span>
                        @endif
                    </td>
                    <td>{{ $f->fornecedor }}</td>
                    <td class="text-end fw-bold text-success">R$ {{ number_format($f->valor_unitario, 2, ',', '.') }}</td>
                    <td class="text-center">
                        <span class="badge {{ $f->fornecedor_ativo == '1' ? 'bg-success' : 'bg-danger' }}">
                            {{ $f->fornecedor_ativo == '1' ? 'Ativo' : 'Inativo' }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </x-tabela-transparencia>
        @include('layouts.partials.back')
    </div>
@endsection
