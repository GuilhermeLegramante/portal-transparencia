@extends('layouts.app')
@section('content')
    <x-breadcrumb :items="['Compras' => '#', 'Requisições' => '#', 'Resumo Anual' => '']" />

    <div class="container-fluid">
        <x-tabela-transparencia :titulo="'Histórico de Requisições ' . $titulo" cor="dark" :colunas="[
            ['label' => 'Exercício', 'align' => 'text-center'],
            ['label' => 'Qtd. Requisições', 'align' => 'text-center'],
            ['label' => 'Valor Total Requisitado', 'align' => 'text-end'],
            ['label' => 'Ação', 'align' => 'text-center'],
        ]">
            @foreach ($dados as $res)
                <tr>
                    <td class="text-center fw-bold">{{ $res->exercicio }}</td>
                    <td class="text-center">{{ $res->quantidade }}</td>
                    <td class="text-end fw-bold">R$ {{ number_format($res->valor_total, 2, ',', '.') }}</td>
                    <td class="text-center"> {{-- 7 --}}
                        <a href="{{ route('compras.requisicao.' . $tipo . '.list', $res->exercicio) }}"
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
