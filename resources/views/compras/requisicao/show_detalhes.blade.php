@extends('layouts.app')
@section('content')
    <x-breadcrumb :items="['Compras' => '#', 'Requisições' => '#', 'Detalhes' => '']" />

    <div class="container-fluid">
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Dados do Requisitante/Fornecedor</h5>
            </div>
            <div class="card-body">
                <h4>{{ $header->nome }}</h4>
                <p class="text-muted mb-0">Exercício: {{ $exercicio }}</p>
            </div>
        </div>

        <x-tabela-transparencia titulo="Itens Requisitados" cor="primary" :colunas="[
            ['label' => 'Data', 'align' => 'text-center'],
            ['label' => 'Item / Complemento', 'align' => 'text-start'],
            ['label' => 'Qtd', 'align' => 'text-center'],
            ['label' => 'Vlr Unitário', 'align' => 'text-end'],
            ['label' => 'Total', 'align' => 'text-end'],
        ]">
            @foreach ($itens as $item)
                <tr>
                    <td class="text-center">{{ \Carbon\Carbon::parse($item->data)->format('d/m/Y') }}</td>
                    <td>
                        <strong>{{ $item->nome }}</strong><br>
                        <small class="text-muted">{{ $item->complemento }}</small>
                    </td>
                    <td class="text-center">{{ number_format($item->quantidade, 2, ',', '.') }}</td>
                    <td class="text-end">R$ {{ number_format($item->valor_unitario, 2, ',', '.') }}</td>
                    <td class="text-end fw-bold">R$ {{ number_format($item->valor_total, 2, ',', '.') }}</td>
                </tr>
            @endforeach
            <tfoot class="table-light">
                <tr>
                    <td colspan="4" class="text-end fw-bold">VALOR TOTAL REQUISITADO:</td>
                    <td class="text-end text-primary fw-bold">R$
                        {{ number_format($itens->sum('valor_total'), 2, ',', '.') }}</td>
                </tr>
            </tfoot>
        </x-tabela-transparencia>
        @include('layouts.partials.back')
    </div>
@endsection
