@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <x-breadcrumb :items="['Sessão' => '#', 'Tramitação de Projeto' => '']" />

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-primary text-white">Dados do Protocolo:
                {{ $protocolo->numero }}/{{ $protocolo->exercicio }}</div>
            <div class="card-body">
                <h5 class="fw-bold">{{ $protocolo->assunto }}</h5>
                <p class="mb-0">{{ $protocolo->descricao }}</p>
                <hr>
                <div class="row small">
                    <div class="col-md-3"><strong>Emissão:</strong> {{ date('d/m/Y', strtotime($protocolo->dataemissao)) }}
                    </div>
                    <div class="col-md-3"><strong>Situação:</strong> <span
                            class="badge bg-info">{{ $protocolo->situacao }}</span></div>
                </div>
            </div>
        </div>

        <h5 class="fw-bold mb-3"><i class="fa fa-history me-2"></i>Histórico de Tramitação (Trâmites)</h5>

        <div class="timeline">
            @foreach ($tramites as $t)
                <div class="card border-0 shadow-sm mb-3 border-start border-4 border-secondary">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span
                                class="badge bg-light text-dark border">{{ date('d/m/Y H:i', strtotime($t->data_despacho)) }}</span>
                            <span class="small text-muted">Recebimento:
                                {{ $t->data_recebimento ? date('d/m/Y', strtotime($t->data_recebimento)) : 'Pendente' }}</span>
                        </div>
                        <div class="row small">
                            <div class="col-md-6 text-danger"><strong>Origem:</strong> {{ $t->setor_origem }}</div>
                            <div class="col-md-6 text-success"><strong>Destino:</strong> {{ $t->setor_destino }}</div>
                        </div>
                        <p class="mt-2 mb-0 small text-muted italic">{{ $t->descricao }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            <a href="javascript:history.back()" class="btn btn-secondary btn-sm">Voltar</a>
        </div>
    </div>
@endsection
