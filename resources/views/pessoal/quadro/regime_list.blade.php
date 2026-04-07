@extends('layouts.app')
@section('content')
    <x-breadcrumb :items="['Pessoal' => '#', 'Quadro Funcional' => '#', 'Por Regime' => '']" />

    <div class="container-fluid">
        {{-- Inclui os cards de resumo e o filtro de situação --}}
        @include('pessoal.quadro._resumo')

        <x-tabela-transparencia titulo="Quadro Funcional por Regime" cor="warning" :colunas="[
            ['label' => 'Código', 'align' => 'text-center'],
            ['label' => 'Descrição do Regime', 'align' => 'text-start'],
            ['label' => 'Quantidade de Contratos', 'align' => 'text-center'],
            ['label' => 'Ação', 'align' => 'text-center'],
        ]">
            @foreach ($dados as $d)
                <tr>
                    <td class="text-center">{{ $d->codigo }}</td>
                    <td>{{ $d->descricao }}</td>
                    <td class="text-center fw-bold">{{ $d->total }}</td>
                    <td class="text-center">
                        <a href="{{ route('pessoal.quadro.detalhes', ['regime', $d->regime_id]) }}?situacao={{ $sitId }}"
                            class="btn btn-sm btn-outline-primary">
                            <i class="fa fa-search"></i> Detalhes
                        </a>
                    </td>
                </tr>
            @endforeach
        </x-tabela-transparencia>
        @include('layouts.partials.back')
    </div>
@endsection
