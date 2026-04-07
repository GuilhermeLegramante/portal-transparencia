@extends('layouts.app')
@section('content')
    <div class="container py-4">
        <x-breadcrumb :items="[
            'Despesa' => '#',
            'Empenho Orçamentário' => route('empenho.elemento.index'),
            'Por Elemento' => '',
        ]" />

        @php
            $columns = [
                ['label' => 'Exercício', 'icone' => 'fa fa-calendar', 'align' => 'text-center'],
                ['label' => 'Total Empenhado', 'icone' => 'fa fa-dollar-sign', 'align' => 'text-end'],
                ['label' => 'Ação', 'icone' => '', 'align' => 'text-center'],
            ];
        @endphp

        <x-tabela-transparencia titulo="Resumo da movimentação por exercício" cor="primary" :colunas="$columns">
            @forelse($resumoAnual as $resumo)
                <tr>
                    <td class="text-center fw-bold">{{ $resumo->exercicio }}</td>
                    <td class="text-end">R$ {{ number_format($resumo->valor, 2, ',', '.') }}</td>
                    <td class="text-center">
                        <a href="{{ route('empenho.elemento.lista', $resumo->exercicio) }}"
                            class="btn btn-action-view btn-sm shadow-sm">
                            <i class="fa fa-eye me-1"></i> Detalhes
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center py-3">Nenhum registro encontrado para este cliente.</td>
                </tr>
            @endforelse
        </x-tabela-transparencia>
        @include('layouts.partials.back')
    </div>
@endsection
