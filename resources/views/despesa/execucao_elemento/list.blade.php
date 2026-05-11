@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <x-breadcrumb cor="primary" :items="[
            'Despesa' => '#',
            'Execução Orçamentária' => route('execucao.elemento.index'),
            'Por Elemento' => '',
        ]" />

        @php
            $orientacaoPDF = 'landscape'; // Padrão para paisagem, pode ser alterado dinamicamente se necessário

            $columns = [
                ['label' => 'Elemento / Descrição', 'icone' => 'tag', 'align' => 'text-start'],
                ['label' => 'Vlr. Orçado', 'icone' => '', 'align' => 'text-end'],
                ['label' => 'Vlr. Corrigido', 'icone' => '', 'align' => 'text-end'],
                ['label' => 'Vlr. Executado', 'icone' => '', 'align' => 'text-end'],
                ['label' => 'Vlr. Restante', 'icone' => '', 'align' => 'text-end'],
                ['label' => '% Comp.', 'icone' => 'chart-pie', 'align' => 'text-center'],
            ];
        @endphp

        <x-tabela-transparencia titulo="Execução Orçamentária por Elemento - {{ $exercicio }}" cor="primary"
            :colunas="$columns">
            @foreach ($elementos as $elemento)
                <tr>
                    {{-- Identificação --}}
                    <td class="text-start align-middle small">
                        <span class="fw-bold text-primary d-block">{{ $elemento->estrutural }}</span>
                        <span class="text-muted">{{ $elemento->descricao }}</span>
                    </td>

                    {{-- Valores --}}
                    <td class="text-end align-middle">R$ {{ number_format($elemento->valor_orcado, 2, ',', '.') }}</td>
                    <td class="text-end align-middle">R$ {{ number_format($elemento->valor_corrigido, 2, ',', '.') }}</td>
                    <td class="text-end align-middle fw-bold">R$ {{ number_format($elemento->valor_executado, 2, ',', '.') }}
                    </td>

                    {{-- Valor Restante (Saldo do Orçamento) --}}
                    <td
                        class="text-end align-middle fw-bold {{ $elemento->valor_restante < 0 ? 'text-danger' : 'text-success' }}">
                        R$ {{ number_format($elemento->valor_restante, 2, ',', '.') }}
                    </td>

                    {{-- Percentual com Progress Bar --}}
                    <td class="text-center align-middle" style="min-width: 120px;">
                        <div class="small mb-1 fw-bold">
                            {{ number_format($elemento->percentual_comprometido, 1, ',', '.') }}%</div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar {{ $elemento->percentual_comprometido > 90 ? 'bg-danger' : ($elemento->percentual_comprometido > 70 ? 'bg-warning' : 'bg-success') }}"
                                role="progressbar" style="width: {{ min($elemento->percentual_comprometido, 100) }}%">
                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach

            <tfoot class="table-light fw-bold">
                <tr class="text-nowrap">
                    <td colspan="1" class="text-end">TOTAIS:</td>
                    <td class="text-end">R$ {{ number_format($elementos->sum('valor_orcado'), 2, ',', '.') }}</td>
                    <td class="text-end">R$ {{ number_format($elementos->sum('valor_corrigido'), 2, ',', '.') }}</td>
                    <td class="text-end">R$ {{ number_format($elementos->sum('valor_executado'), 2, ',', '.') }}</td>
                    <td class="text-end">R$ {{ number_format($elementos->sum('valor_restante'), 2, ',', '.') }}</td>
                    <td class="text-center">
                        @php
                            $totalOrcado = $elementos->sum('valor_orcado') + $elementos->sum('valor_corrigido');
                            $mediaPercentual =
                                $totalOrcado > 0 ? ($elementos->sum('valor_executado') / $totalOrcado) * 100 : 0;
                        @endphp
                        {{ number_format($mediaPercentual, 1, ',', '.') }}%
                    </td>
                </tr>
            </tfoot>
        </x-tabela-transparencia>
        @include('layouts.partials.back')
    </div>
@endsection
