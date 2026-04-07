@extends('layouts.app')

@section('content')
    <x-breadcrumb :items="[
        'Compras' => '#',
        'Licitações' => route('compras.licitacoes.processo.index'),
        'Exercício ' . $exercicio => '',
    ]" />

    <div class="container-fluid">
        @php
            $columns = [
                ['label' => 'Processo/Edital', 'icone' => 'fa fa-hashtag', 'align' => 'text-center'], // 1
                ['label' => 'Modalidade/Tipo', 'icone' => 'fa fa-gavel', 'align' => 'text-start'], // 2
                ['label' => 'Objeto', 'icone' => 'fa fa-file-alt', 'align' => 'text-start'], // 3
                ['label' => 'Abertura', 'icone' => 'fa fa-calendar-alt', 'align' => 'text-center'], // 4
                ['label' => 'Situação', 'icone' => 'fa fa-info-circle', 'align' => 'text-center'], // 5
                ['label' => 'Valor Total', 'icone' => 'fa fa-coins', 'align' => 'text-end'], // 6
                ['label' => 'Ação', 'icone' => '', 'align' => 'text-center'], // 7
            ];
        @endphp

        <x-tabela-transparencia titulo="Processos Licitatórios - {{ $exercicio }}" cor="primary" :colunas="$columns">
            @forelse($dados as $item)
                @php
                    // Mapeamento de Situação para Cores/Texto
                    $statusArr = [
                        0 => ['label' => 'ABERTO', 'cor' => 'bg-primary'],
                        1 => ['label' => 'HOMOLOGADO', 'cor' => 'bg-success'],
                        2 => ['label' => 'ADJUDICADA', 'cor' => 'bg-info text-dark'],
                        3 => ['label' => 'IMPUGNADO', 'cor' => 'bg-warning text-dark'],
                        4 => ['label' => 'CANCELADO', 'cor' => 'bg-danger'],
                        5 => ['label' => 'REVOGADO', 'cor' => 'bg-secondary'],
                        6 => ['label' => 'SUSPENSO', 'cor' => 'bg-dark'],
                        7 => ['label' => 'EDITAL', 'cor' => 'bg-info'],
                        8 => ['label' => 'DESERTA', 'cor' => 'bg-warning'],
                    ];
                    $status = $statusArr[$item->situacao_id] ?? ['label' => 'N/D', 'cor' => 'bg-light text-dark'];
                @endphp
                <tr>
                    {{-- 1. Processo/Edital --}}
                    <td class="text-center fw-bold">
                        <span class="d-block">P: {{ $item->numero_processo }}</span>
                        <small class="text-muted">E: {{ $item->numero_edital }}</small>
                    </td>

                    {{-- 2. Modalidade/Tipo --}}
                    <td>
                        <span class="fw-bold d-block">{{ $item->modalidade_nome }}</span>
                        <small class="text-muted small">
                            @switch($item->tipo_id)
                                @case(1)
                                    MENOR PREÇO GLOBAL
                                @break

                                @case(2)
                                    MENOR PREÇO POR ITEM
                                @break

                                @case(3)
                                    MAIOR LANCE OU OFERTA
                                @break

                                @case(4)
                                    MELHOR TÉCNICA
                                @break

                                @case(5)
                                    TÉCNICA E PREÇO
                                @break

                                @case(25)
                                    NÃO SE APLICA
                                @break

                                @case(26)
                                    MENOR TAXA
                                @break

                                @case(27)
                                    MAIOR DESCONTO
                                @break

                                @case(31)
                                    MELHOR CONTEÚDO ARTÍSTICO
                                @break

                                @default
                                    TIPO #{{ $item->tipo_id }}
                            @endswitch
                        </small>
                    </td>

                    {{-- 3. Objeto --}}
                    <td style="max-width: 300px;">
                        <span class="text-truncate d-block" title="{{ $item->objeto }}">
                            {{ Str::limit($item->objeto, 80) }}
                        </span>
                    </td>

                    {{-- 4. Abertura --}}
                    <td class="text-center">
                        {{ $item->data_abertura ? \Carbon\Carbon::parse($item->data_abertura)->format('d/m/Y') : '--' }}
                    </td>

                    {{-- 5. Situação --}}
                    <td class="text-center">
                        <span class="badge {{ $status['cor'] }} shadow-sm">
                            {{ $status['label'] }}
                        </span>
                    </td>

                    {{-- 6. Valor --}}
                    <td class="text-end fw-bold">
                        R$ {{ number_format($item->valor_total, 2, ',', '.') }}
                    </td>

                    {{-- 7. Ação --}}
                    <td class="text-center">
                        <a href="{{ route('compras.licitacoes.processo.show', $item->licitacao_id) }}"
                            class="btn btn-sm btn-outline-primary border-0 shadow-sm">
                            <i class="fa fa-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">Nenhum processo encontrado.</td>
                    </tr>
                @endforelse
            </x-tabela-transparencia>
            @include('layouts.partials.back')
        </div>
    @endsection
