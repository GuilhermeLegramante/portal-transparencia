@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <x-breadcrumb cor="primary" :items="[
            'Despesa' => '#',
            'Resumo por Elemento' => route('empenho.elemento.lista', $exercicio),
            'Empenhos do Elemento' => '',
        ]" />

        @include('layouts.partials.elemento-despesa')

        <x-tabela-transparencia titulo="Empenhos Vinculados - Exercício {{ $exercicio }}" cor="primary" :colunas="$columns">
            @foreach ($empenhos as $emp)
                <tr>
                    {{-- Ação: Ver o empenho completo --}}
                    <td class="text-center align-middle">
                        <a href="{{ route('empenho.elemento.empenho.detalhe', [
                            'exercicio' => $exercicio,
                            'elemento_id' => $elemento->id, // Você precisa dessa variável vinda do Controller
                            'empenho_id' => $emp->empenho_id,
                        ]) }}"
                            class="btn btn-sm btn-outline-primary" title="Ver Detalhes do Empenho">
                            <i class="fa fa-search"></i>
                        </a>
                    </td>

                    <td class="text-center align-middle fw-bold">
                        {{ $emp->numero }}
                    </td>

                    <td class="text-center align-middle">
                        {{ date('d/m/Y', strtotime($emp->data_emissao)) }}
                    </td>

                    <td class="text-start align-middle small">
                        {{ $emp->nome ?? 'CREDOR NÃO IDENTIFICADO' }}
                    </td>

                    <td class="text-end align-middle">
                        {{ number_format($emp->total_empenhado, 2, ',', '.') }}
                    </td>

                    <td class="text-end align-middle">
                        {{ number_format($emp->total_liquidado, 2, ',', '.') }}
                    </td>

                    <td class="text-end align-middle">
                        {{ number_format($emp->total_pago, 2, ',', '.') }}
                    </td>

                    <td class="text-end align-middle fw-bold text-danger">
                        {{ number_format($emp->saldo_pagar, 2, ',', '.') }}
                    </td>
                </tr>
            @endforeach

            <tfoot class="table-light fw-bold">
                <tr class="text-nowrap">
                    <td colspan="4" class="text-end">TOTALIZADORES:</td>
                    <td class="text-end">R$ {{ number_format($empenhos->sum('total_empenhado'), 2, ',', '.') }}</td>
                    <td class="text-end">R$ {{ number_format($empenhos->sum('total_liquidado'), 2, ',', '.') }}</td>
                    <td class="text-end">R$ {{ number_format($empenhos->sum('total_pago'), 2, ',', '.') }}</td>
                    <td class="text-end text-danger">R$ {{ number_format($empenhos->sum('saldo_pagar'), 2, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        </x-tabela-transparencia>

        @include('layouts.partials.back')
    </div>
@endsection
