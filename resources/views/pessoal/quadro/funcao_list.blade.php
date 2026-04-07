@extends('layouts.app')
@section('content')
    <x-breadcrumb :items="['Pessoal' => '#', 'Quadro Funcional' => '#', 'Por Função' => '']" />

    <div class="container-fluid">
        @include('pessoal.quadro._resumo')

        <x-tabela-transparencia titulo="Quadro por Função" cor="primary" :colunas="[
            ['label' => 'Código', 'align' => 'text-center'],
            ['label' => 'Descrição da Função', 'align' => 'text-start'],
            ['label' => 'Qtd. Contratos', 'align' => 'text-center'],
            ['label' => 'Ação', 'align' => 'text-center'],
        ]">
            @foreach ($dados as $d)
                <tr>
                    <td class="text-center">{{ $d->codigo }}</td>
                    <td>{{ $d->descricao }}</td>
                    <td class="text-center fw-bold">{{ $d->total }}</td>
                    <td class="text-center">
                        <a href="{{ route('pessoal.quadro.detalhes', ['funcao', $d->funcao_id]) }}?situacao={{ $sitId }}"
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
