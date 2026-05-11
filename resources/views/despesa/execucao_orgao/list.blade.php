@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <x-breadcrumb cor="primary" :items="[
            'Despesa' => '#',
            'Execução Orçamentária' => route('execucao.orgao.index'),
            'Por Órgão' => '',
        ]" />

        @php
            $orientacaoPDF = 'landscape'; // Padrão para paisagem, pode ser alterado dinamicamente se necessário

            $columns = [
                ['label' => 'Órgão / Descrição', 'icone' => 'building', 'align' => 'text-start'],
                ['label' => 'Vlr. Orçado', 'icone' => '', 'align' => 'text-end'],
                ['label' => 'Vlr. Corrigido', 'icone' => '', 'align' => 'text-end'],
                ['label' => 'Vlr. Executado', 'icone' => '', 'align' => 'text-end'],
                ['label' => 'Vlr. Restante', 'icone' => '', 'align' => 'text-end'],
                ['label' => '% Comp.', 'icone' => 'chart-pie', 'align' => 'text-center'],
            ];
        @endphp

        <x-tabela-transparencia titulo="Execução Orçamentária por Órgão - {{ $exercicio }}" cor="primary"
            :colunas="$columns">
            @foreach ($data as $item)
                <tr>
                    {{-- Identificação --}}
                    <td class="text-start align-middle small">
                        <span class="fw-bold text-primary d-block">{{ $item->codigo }}</span>
                        <span class="text-muted">{{ $item->descricao }}</span>
                    </td>

                    {{-- Valores --}}
                    <td class="text-end align-middle">R$ {{ number_format($item->valor_orcado, 2, ',', '.') }}</td>
                    <td class="text-end align-middle">R$ {{ number_format($item->valor_corrigido, 2, ',', '.') }}</td>
                    <td class="text-end align-middle fw-bold">R$ {{ number_format($item->valor_executado, 2, ',', '.') }}
                    </td>

                    {{-- Valor Restante (Saldo do Orçamento) --}}
                    <td
                        class="text-end align-middle fw-bold {{ $item->valor_restante < 0 ? 'text-danger' : 'text-success' }}">
                        R$ {{ number_format($item->valor_restante, 2, ',', '.') }}
                    </td>

                    {{-- Percentual com Progress Bar --}}
                    <td class="text-center align-middle" style="min-width: 120px;">
                        <div class="small mb-1 fw-bold">
                            {{ number_format($item->percentual_comprometido, 1, ',', '.') }}%</div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar {{ $item->percentual_comprometido > 90 ? 'bg-danger' : ($item->percentual_comprometido > 70 ? 'bg-warning' : 'bg-success') }}"
                                role="progressbar" style="width: {{ min($item->percentual_comprometido, 100) }}%">
                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach

            <tfoot class="table-light fw-bold">
                <tr class="text-nowrap">
                    <td colspan="1" class="text-end">TOTAIS:</td>
                    <td class="text-end">R$ {{ number_format($data->sum('valor_orcado'), 2, ',', '.') }}</td>
                    <td class="text-end">R$ {{ number_format($data->sum('valor_corrigido'), 2, ',', '.') }}</td>
                    <td class="text-end">R$ {{ number_format($data->sum('valor_executado'), 2, ',', '.') }}</td>
                    <td class="text-end">R$ {{ number_format($data->sum('valor_restante'), 2, ',', '.') }}</td>
                    <td class="text-center">
                        @php
                            $totalOrcado = $data->sum('valor_orcado') + $data->sum('valor_corrigido');
                            $mediaPercentual =
                                $totalOrcado > 0 ? ($data->sum('valor_executado') / $totalOrcado) * 100 : 0;
                        @endphp
                        {{ number_format($mediaPercentual, 1, ',', '.') }}%
                    </td>
                </tr>
            </tfoot>
        </x-tabela-transparencia>
        @include('layouts.partials.back')
    </div>
@endsection
