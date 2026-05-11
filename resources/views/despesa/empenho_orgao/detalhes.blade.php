@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <x-breadcrumb cor="primary" :items="[
            'Despesa' => '#',
            'Resumo por Órgão' => route('empenho.orgao.lista', $exercicio),
            'Órgão: ' . $orgao->codigo => '',
        ]" />

        {{-- Card de Identificação (Laranja) --}}
        @include('layouts.partials.orgao')

        {{-- Tabela de Empenhos (Azul) --}}
        <x-tabela-transparencia titulo="Empenhos Orçamentários emitidos para o exercício {{ $exercicio }}" cor="primary"
            :colunas="$columns">
            @foreach ($empenhos as $emp)
                <tr>
                    {{-- Ação: Ver o empenho completo --}}
                    <td class="text-center align-middle">
                        <a href="{{ route('empenho.orgao.empenho.detalhe', [
                            'exercicio' => $exercicio,
                            'orgao_id' => $orgao->id, // Você precisa dessa variável vinda do Controller
                            'empenho_id' => $emp->empenho_id,
                        ]) }}"
                            class="btn btn-sm btn-outline-secondary" title="Ver Detalhes do Empenho">
                            <i class="fa fa-search"></i>
                        </a>
                    </td>

                    <td class="text-center align-middle">{{ $emp->numero }}</td>

                    <td class="text-center align-middle">
                        {{ date('d/m/Y', strtotime($emp->data_emissao)) }}
                    </td>

                    <td class="text-end align-middle">
                        {{ number_format($emp->saldo_empenhado, 2, ',', '.') }}
                    </td>

                    <td class="text-end align-middle">
                        {{ number_format($emp->saldo_liquidar, 2, ',', '.') }}
                    </td>

                    <td class="text-end align-middle fw-bold text-primary">
                        {{ number_format($emp->saldo_pagar, 2, ',', '.') }}
                    </td>
                </tr>
            @endforeach

            <tfoot class="table-light fw-bold">
                <tr class="text-nowrap">
                    <td colspan="3" class="text-end">TOTAIS DO ÓRGÃO:</td>
                    <td class="text-end">R$ {{ number_format($empenhos->sum('saldo_empenhado'), 2, ',', '.') }}</td>
                    <td class="text-end">R$ {{ number_format($empenhos->sum('saldo_liquidar'), 2, ',', '.') }}</td>
                    <td class="text-end text-primary">R$ {{ number_format($empenhos->sum('saldo_pagar'), 2, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        </x-tabela-transparencia>

        @include('layouts.partials.back')
    </div>
@endsection
