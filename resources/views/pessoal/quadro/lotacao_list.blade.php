@extends('layouts.app')
@section('content')
    <x-breadcrumb :items="['Pessoal' => '#', 'Quadro Funcional' => '#', 'Por Lotação' => '']" />

    <div class="container-fluid">
        @include('pessoal.quadro._resumo')

        <x-tabela-transparencia titulo="Quadro por Lotação" cor="info" :colunas="[
            ['label' => 'Unidade', 'align' => 'text-start'],
            ['label' => 'Lotação', 'align' => 'text-start'],
            ['label' => 'Qtd. Contratos', 'align' => 'text-center'],
            ['label' => 'Ação', 'align' => 'text-center'],
        ]">
            @foreach ($dados as $d)
                <tr>
                    <td>{{ $d->unidade }}</td>
                    <td><strong>{{ $d->codigo }}</strong> - {{ $d->descricao }}</td>
                    <td class="text-center fw-bold">{{ $d->total }}</td>
                    <td class="text-center">
                        <a href="{{ route('pessoal.quadro.detalhes', ['lotacao', $d->lotacao_id]) }}?situacao={{ $sitId }}"
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
