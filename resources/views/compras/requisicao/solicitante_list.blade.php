@extends('layouts.app')
@section('content')
    <x-breadcrumb :items="[
        'Compras' => '#',
        'Requisições' => route('compras.requisicao.solicitante.index'),
        'Exercício ' . $exercicio => '',
    ]" />

    <div class="container-fluid">
        <x-tabela-transparencia :titulo="'Requisições por Solicitante - Exercício ' . $exercicio" cor="primary" :colunas="[
            ['label' => 'Nome do Solicitante', 'align' => 'text-start'],
            ['label' => 'Qtd. Requisições', 'align' => 'text-center'],
            ['label' => 'Total Requisitado', 'align' => 'text-end'],
            ['label' => 'Ação', 'align' => 'text-center'],
        ]">
            @foreach ($dados as $d)
                <tr>
                    <td>{{ $d->nome }}</td>
                    <td class="text-center">{{ $d->quantidade_requisicao }}</td>
                    <td class="text-end fw-bold">R$ {{ number_format($d->total_requisitado, 2, ',', '.') }}</td>
                    <td class="text-center">
                        <a href="{{ route('compras.requisicao.solicitante.show', [$exercicio, $d->unidade_id]) }}"
                            class="btn btn-sm btn-outline-primary">
                            <i class="fa fa-search"></i> Itens
                        </a>
                    </td>
                </tr>
            @endforeach

            <tfoot class="table-light fw-bold">
                <tr class="text-nowrap">
                    <td class="text-end">TOTAIS:</td>

                    <td class="text-center">
                        {{ $dados->sum('quantidade_requisicao') }}
                    </td>

                    <td class="text-end text-primary">
                        R$ {{ number_format($dados->sum('total_requisitado'), 2, ',', '.') }}
                    </td>

                    <td></td> {{-- Coluna de Ação vazia --}}
                </tr>
            </tfoot>
        </x-tabela-transparencia>
        @include('layouts.partials.back')
    </div>
@endsection
