@extends('layouts.app')

@section('content')
    <x-breadcrumb :items="[
        'Compras' => '#',
        'Contratos' => route('compras.licitacoes.contrato.index'),
        'Exercício ' . $exercicio => '',
    ]" />

    <div class="container-fluid">
        @php
            $columns = [
                ['label' => 'Nº Contrato', 'icone' => 'fa fa-file-contract', 'align' => 'text-center'], // 1
                ['label' => 'Fornecedor', 'icone' => 'fa fa-user-tie', 'align' => 'text-start'], // 2
                ['label' => 'Assinatura', 'icone' => 'fa fa-pen-fancy', 'align' => 'text-center'], // 3
                ['label' => 'Vigência (Início/Fim)', 'icone' => 'fa fa-calendar-alt', 'align' => 'text-center'], // 4
                ['label' => 'Situação', 'icone' => 'fa fa-info-circle', 'align' => 'text-center'], // 5
                ['label' => 'Valor', 'icone' => 'fa fa-coins', 'align' => 'text-end'], // 6
                ['label' => 'Ação', 'icone' => '', 'align' => 'text-center'], // 7
            ];
        @endphp

        <x-tabela-transparencia titulo="Contratos Administrativos - Exercício {{ $exercicio }}" cor="primary"
            :colunas="$columns">
            @forelse($dados as $item)
                @php
                    // Mapeamento de Cores para Situação
                    $corSituacao = [
                        'ATV' => 'bg-success',
                        'RES' => 'bg-danger',
                        'SUS' => 'bg-warning text-dark',
                        'ANL' => 'bg-dark',
                        'ENC' => 'bg-secondary',
                    ];

                    $labelSituacao = [
                        'ATV' => 'ATIVO',
                        'RES' => 'RESCINDIDO',
                        'SUS' => 'SUSPENSO',
                        'ANL' => 'ANULADO',
                        'ENC' => 'ENCERRADO',
                    ];
                @endphp
                <tr>
                    {{-- 1. Número --}}
                    <td class="text-center fw-bold">{{ $item->numero }}</td>

                    {{-- 2. Fornecedor --}}
                    <td>
                        <span class="d-block text-truncate" style="max-width: 250px;" title="{{ $item->nome_fornecedor }}">
                            {{ $item->nome_fornecedor }}
                        </span>
                    </td>

                    {{-- 3. Data Assinatura --}}
                    <td class="text-center">
                        {{ \Carbon\Carbon::parse($item->dataassinatura)->format('d/m/Y') }}
                    </td>

                    {{-- 4. Vigência --}}
                    <td class="text-center small">
                        <span class="text-success">I:
                            {{ \Carbon\Carbon::parse($item->datainicio)->format('d/m/Y') }}</span><br>
                        <span class="text-danger">F:
                            {{ $item->data_termino ? \Carbon\Carbon::parse($item->data_termino)->format('d/m/Y') : '---' }}</span>
                    </td>

                    {{-- 5. Situação --}}
                    <td class="text-center">
                        <span class="badge {{ $corSituacao[$item->situacao] ?? 'bg-light text-dark' }} shadow-sm">
                            {{ $labelSituacao[$item->situacao] ?? $item->situacao }}
                        </span>
                    </td>

                    {{-- 6. Valor --}}
                    <td class="text-end fw-bold">
                        R$ {{ number_format($item->valor, 2, ',', '.') }}
                    </td>

                    {{-- 7. Ação --}}
                    <td class="text-center">
                        <a href="{{ route('compras.licitacoes.contrato.show', $item->id) }}"
                            class="btn btn-sm btn-outline-primary border-0 shadow-sm"
                            title="Ver detalhes do contrato e ocorrências">
                            <i class="fa fa-eye"></i>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">
                        Nenhum contrato encontrado para o exercício de {{ $exercicio }}.
                    </td>
                </tr>
            @endforelse
        </x-tabela-transparencia>

        @include('layouts.partials.back')
    </div>
@endsection
