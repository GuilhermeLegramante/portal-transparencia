@extends('layouts.app')
@section('content')
    <x-breadcrumb :items="[
        'Compras' => '#',
        'Contratos' => route('compras.licitacoes.contrato.index'),
        'Detalhes do Contrato' => '',
    ]" />

    <div class="container-fluid">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Contrato nº {{ $contrato->numero }}</h5>
                <span class="badge {{ $contrato->situacao == 'ATV' ? 'bg-success' : 'bg-secondary' }}">
                    {{ $contrato->situacao }}
                </span>
            </div>
            <div class="card-body">
                <p class="mb-1"><strong>Fornecedor:</strong> {{ $contrato->fornecedor_nome }}</p>
                <p class="text-muted small"><strong>Objeto:</strong> {{ $contrato->objeto ?? 'Não informado' }}</p>
            </div>
        </div>

        <x-tabela-transparencia titulo="Histórico de Ocorrências (Início/Aditivos/Rescisão)" cor="info" :colunas="[
            ['label' => 'Tipo', 'icone' => '', 'align' => 'text-start'],
            ['label' => 'Assinatura', 'icone' => '', 'align' => 'text-center'],
            ['label' => 'Vigência', 'icone' => '', 'align' => 'text-center'],
            ['label' => 'Resumo', 'icone' => '', 'align' => 'text-start'],
            ['label' => 'Valor', 'icone' => '', 'align' => 'text-end'],
        ]">
            @foreach ($ocorrencias as $oc)
                <tr>
                    <td>
                        <span class="badge border text-dark">Cód. {{ $oc->codigo }}</span>
                    </td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($oc->dataassinatura)->format('d/m/Y') }}</td>
                    <td class="text-center">
                        <small>De: {{ \Carbon\Carbon::parse($oc->datainicio)->format('d/m/Y') }}</small><br>
                        <small>Até:
                            {{ $oc->datatermino ? \Carbon\Carbon::parse($oc->datatermino)->format('d/m/Y') : '---' }}</small>
                    </td>
                    <td class="small" style="max-width: 300px;">{{ $oc->resumo }}</td>
                    <td class="text-end fw-bold">R$ {{ number_format($oc->valor, 2, ',', '.') }}</td>
                </tr>
            @endforeach
        </x-tabela-transparencia>
        @include('layouts.partials.back')
    </div>
@endsection
