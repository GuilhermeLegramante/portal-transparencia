@extends('layouts.app')

@section('content')
    <div class="container-fluid px-lg-5 py-4 bg-light-gray min-vh-100">
        <x-breadcrumb :items="$breadcrumb" />

        <div class="row mb-4 mt-3 align-items-center">
            <div class="col">
                <h1 class="fw-bold text-dark border-start border-primary border-5 ps-3">
                    Pedido #{{ $pedido->id }}
                </h1>
                <p class="text-muted fs-5 mb-0">{{ $pedido->titulo }}</p>
            </div>
            <div class="col-auto">
                <span class="badge bg-{{ $pedido->situacao == 'A' ? 'primary' : 'success' }} rounded-pill px-3 py-2 fs-6">
                    {{ $pedido->situacao == 'A' ? 'Aberto' : 'Fechado' }}
                </span>
            </div>
        </div>

        {{-- Detalhes principais --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3"><i class="fas fa-info-circle me-2 text-primary"></i>Descrição Inicial</h5>
                <p class="text-secondary">{{ $pedido->titulo }}</p>
                <div class="text-muted small mt-3">
                    <i class="far fa-calendar-alt me-1"></i> Criado em:
                    {{ \Carbon\Carbon::parse($pedido->datahora)->format('d/m/Y H:i') }}
                </div>
            </div>
        </div>

        {{-- Histórico de Mensagens --}}
        <h5 class="fw-bold mb-3">Histórico e Respostas</h5>
        <div class="d-flex flex-column gap-3">
            @foreach ($mensagens as $msg)
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="fw-bold text-primary">
                                {{ $loop->first ? 'Você' : 'Atendente/Sistema' }}
                            </span>
                            <span class="text-muted small">
                                {{ \Carbon\Carbon::parse($msg->datahora)->format('d/m/Y H:i') }}
                            </span>
                        </div>
                        <p class="mb-2">{{ $msg->mensagem }}</p>

                        @if ($msg->anexo)
                            <hr>
                            <a href="{{ Storage::url($msg->anexo) }}" target="_blank"
                                class="btn btn-sm btn-outline-primary rounded-pill">
                                <i class="fas fa-paperclip me-2"></i>Ver Anexo
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            <a href="{{ route('sic.pedidos') }}" class="btn btn-outline-secondary rounded-3 px-4">
                Voltar para lista
            </a>
        </div>
    </div>
@endsection
