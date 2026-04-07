@extends('layouts.app')

@section('content')
    <x-breadcrumb :items="[
        'Receita' => '#',
        'Execução Orçamentária' => route('receita.execucao.elemento.index'),
        'Por Elemento' => '',
        'Exercício ' . $exercicio => '',
    ]" />

    <div class="container-fluid">
        @php
            $columns = [
                ['label' => 'Estrutural', 'icone' => 'fa fa-list-ol', 'align' => 'text-center'],
                ['label' => 'Descrição', 'icone' => 'fa fa-info-circle', 'align' => 'text-start'],
                ['label' => 'Vlr. Orçado', 'icone' => 'fa fa-coins', 'align' => 'text-end'],
                ['label' => 'Vlr. Executado', 'icone' => 'fa fa-check-double', 'align' => 'text-end'],
                ['label' => 'Diferença', 'icone' => 'fa fa-balance-scale', 'align' => 'text-end'],
            ];
        @endphp

        <x-tabela-transparencia titulo="Execução por Elemento de Receita - {{ $exercicio }}" cor="primary" :colunas="$columns">
            @foreach($dados as $item)
                @php 
                    $dif = ($item->valor_executado ?? 0) - ($item->valor_orcado ?? 0); 
                @endphp
                <tr>
                    <td class="text-center text-muted small">{{ $item->estrutural }}</td>
                    <td class="fw-bold">{{ $item->descricao }}</td>
                    <td class="text-end">R$ {{ number_format($item->valor_orcado ?? 0, 2, ',', '.') }}</td>
                    <td class="text-end text-primary fw-bold">R$ {{ number_format($item->valor_executado ?? 0, 2, ',', '.') }}</td>
                    <td class="text-end {{ $dif >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ $dif >= 0 ? '+' : '' }} {{ number_format($dif, 2, ',', '.') }}
                    </td>
                </tr>
            @endforeach
        </x-tabela-transparencia>
        @include('layouts.partials.back')
    </div>
@endsection