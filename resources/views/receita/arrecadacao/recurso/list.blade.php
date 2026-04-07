@extends('layouts.app')

@section('content')
    <x-breadcrumb :items="[
        'Receita' => '#',
        'Arrecadação' => route('receita.arrecadacao.recurso.index'),
        'Lista ' . $exercicio => '',
    ]" />

    <x-tabela-transparencia titulo="Arrecadação por Recurso - {{ $exercicio }}" cor="primary" :colunas="[
        ['label' => '', 'icone' => 'search', 'align' => 'text-center'],
        ['label' => 'Código', 'icone' => '', 'align' => 'text-center'],
        ['label' => 'Descrição', 'icone' => '', 'align' => 'text-start'],
        ['label' => 'Vlr. Arrecadado', 'icone' => '', 'align' => 'text-end'],
    ]">
        @foreach ($dados as $item)
            <tr>
                <td class="text-center align-middle">
                    <a href="{{ route('receita.arrecadacao.recurso.details', [$exercicio, $item->recurso_id]) }}"
                        class="btn btn-sm btn-outline-secondary" title="Ver detalhes">
                        <i class="fa fa-search"></i>
                    </a>
                </td>
                <td class="text-center">{{ $item->codigo }}</td>
                <td>{{ $item->descricao }}</td>
                <td class="text-end">R$ {{ number_format($item->valor_arrecadado, 2, ',', '.') }}</td>
            </tr>
        @endforeach
    </x-tabela-transparencia>
    @include('layouts.partials.back')
@endsection
