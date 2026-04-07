@extends('layouts.app')
@section('content')
    <x-breadcrumb :items="[
        'Compras' => '#',
        'Requisições por Fornecedor' => route('compras.requisicao.fornecedor.index'),
        'Exercício ' . $exercicio => '',
    ]" />

    <div class="container-fluid">
        <x-tabela-transparencia :titulo="'Fornecedores com Itens Requisitados - ' . $exercicio" cor="primary" :colunas="[
            ['label' => 'Fornecedor', 'align' => 'text-start'],
            ['label' => 'Qtd. Requisições', 'align' => 'text-center'],
            ['label' => 'Total Requisitado', 'align' => 'text-end'],
            ['label' => 'Ação', 'align' => 'text-center'],
        ]">
            @foreach ($dados as $d)
                <tr>
                    {{-- Coluna 1: Nome do Fornecedor --}}
                    <td>{{ $d->nome ?? 'Fornecedor não identificado' }}</td>

                    {{-- Coluna 2: Quantidade de Requisições --}}
                    <td class="text-center">{{ $d->quantidade_requisicao }}</td>

                    {{-- Coluna 3: Valor Total --}}
                    <td class="text-end fw-bold">R$ {{ number_format($d->total_requisitado, 2, ',', '.') }}</td>

                    {{-- Coluna 4: Ação --}}
                    <td class="text-center">
                        <a href="{{ route('compras.requisicao.fornecedor.show', [$exercicio, $d->municipe_id]) }}"
                            class="btn btn-sm btn-outline-primary shadow-sm">
                            <i class="fa fa-search me-1"></i> Detalhes
                        </a>
                    </td>
                </tr>
            @endforeach
        </x-tabela-transparencia>

        @include('layouts.partials.back')
    </div>
@endsection
