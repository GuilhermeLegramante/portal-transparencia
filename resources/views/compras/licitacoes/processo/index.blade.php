@extends('layouts.app')

@section('content')
    <x-breadcrumb :items="[
        'Compras' => '#',
        'Licitações' => '#',
        'Processo Licitatório' => '',
    ]" />

    <div class="container-fluid">
        @php
            $columns = [
                ['label' => 'Exercício', 'icone' => 'fa fa-calendar', 'align' => 'text-center'], // 1
                ['label' => 'Edital', 'icone' => '', 'align' => 'text-center'], // 2
                ['label' => 'Aberta', 'icone' => '', 'align' => 'text-center'], // 3
                ['label' => 'Finalizada', 'icone' => '', 'align' => 'text-center'], // 4
                ['label' => 'Suspensa', 'icone' => '', 'align' => 'text-center'], // 5
                ['label' => 'Total', 'icone' => 'fa fa-list', 'align' => 'text-center'], // 6
                ['label' => 'Ação', 'icone' => '', 'align' => 'text-center'], // 7
            ];
        @endphp

        <x-tabela-transparencia titulo="Resumo por Exercício" cor="dark" :colunas="$columns">
            @forelse($resumoAnual as $resumo)
                <tr>
                    <td class="text-center fw-bold">{{ $resumo->exercicio }}</td> {{-- 1 --}}
                    <td class="text-center">{{ $resumo->edital }}</td> {{-- 2 --}}
                    <td class="text-center">{{ $resumo->aberta }}</td> {{-- 3 --}}
                    <td class="text-center">{{ $resumo->finalizada }}</td> {{-- 4 --}}
                    <td class="text-center">{{ $resumo->suspensa }}</td> {{-- 5 --}}
                    <td class="text-center fw-bold">{{ $resumo->total }}</td> {{-- 6 --}}
                    <td class="text-center"> {{-- 7 --}}
                        <a href="{{ route('compras.licitacoes.processo.list', $resumo->exercicio) }}"
                            class="btn btn-sm btn-primary">
                            <i class="fa fa-search"></i>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    {{-- CRÍTICO: O colspan deve ser exatamente 7 --}}
                    <td colspan="7" class="text-center py-4">Nenhum registro encontrado.</td>
                </tr>
            @endforelse
        </x-tabela-transparencia>
        @include('layouts.partials.back')
    </div>
@endsection
