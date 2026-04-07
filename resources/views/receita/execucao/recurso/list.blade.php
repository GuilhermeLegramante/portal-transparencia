@extends('layouts.app')

@section('content')
    {{-- Breadcrumb seguindo seu padrão --}}
    <x-breadcrumb :items="[
        'Receita' => '#',
        'Execução Orçamentária' => route('receita.execucao.recurso.index'),
        'Por Recurso' => '',
        'Exercício ' . $exercicio => '',
    ]" />

    <div class="container-fluid">
        @php
            $orientacaoPDF = 'landscape'; // Padrão para paisagem, pode ser alterado dinamicamente se necessário

            $columns = [
                ['label' => 'Código', 'icone' => 'fa fa-barcode', 'align' => 'text-center'],
                ['label' => 'Descrição do Recurso', 'icone' => 'fa fa-info-circle', 'align' => 'text-start'],
                ['label' => 'Vlr. Orçado', 'icone' => 'fa fa-coins', 'align' => 'text-end'],
                ['label' => 'Vlr. Realizado', 'icone' => 'fa fa-check-double', 'align' => 'text-end'],
                ['label' => '% Realizado', 'icone' => 'fa fa-chart-pie', 'align' => 'text-center'],
            ];
        @endphp

        <x-tabela-transparencia titulo="Execução por Fonte de Recurso - {{ $exercicio }}" cor="primary" :colunas="$columns">

            @forelse($dados as $item)
                <tr>
                    <td class="text-center fw-bold text-muted">{{ $item->codigo }}</td>
                    <td>
                        <span class="d-block fw-bold">{{ $item->descricao }}</span>
                        {{-- <small class="text-muted text-uppercase">ID Recurso: {{ $item->recurso_id }}</small> --}}
                    </td>

                    <td class="text-end">
                        R$ {{ number_format($item->valor_orcado ?? 0, 2, ',', '.') }}
                    </td>

                    <td class="text-end fw-bold text-primary">
                        R$ {{ number_format($item->valor_executado ?? 0, 2, ',', '.') }}
                    </td>

                    <td class="text-center">
                        @php
                            $porcentagem =
                                $item->valor_orcado > 0 ? ($item->valor_executado / $item->valor_orcado) * 100 : 0;
                        @endphp
                        <x-percentual-progress :valor="$porcentagem" />
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">
                        Nenhuma movimentação de recurso encontrada para {{ $exercicio }}.
                    </td>
                </tr>
            @endforelse
        </x-tabela-transparencia>
        @include('layouts.partials.back')
    </div>
@endsection
