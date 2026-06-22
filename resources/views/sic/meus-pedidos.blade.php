@extends('layouts.app')

@section('content')
    <div class="container-fluid px-lg-5 py-4 bg-light-gray min-vh-100">
        <x-breadcrumb :items="$breadcrumb" />

        {{-- Mensagens --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 rounded-4 mb-4" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 rounded-4 mb-4" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 rounded-4 mb-4" role="alert">
                <div class="fw-bold mb-1">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Não foi possível concluir a operação.
                </div>
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
            </div>
        @endif

        {{-- Cabeçalho --}}
        <div class="row mb-4 mt-3">
            <div class="col-md-8">
                <h1 class="fw-bold text-dark border-start border-primary border-5 ps-3">Meus Pedidos</h1>
                <p class="text-muted fs-5 mb-0">
                    Acompanhe suas solicitações no Serviço de Informação ao Cidadão - {{ config('app.client_full_name') }}
                </p>
            </div>

            <div class="col-md-4 text-md-end d-flex align-items-center justify-content-md-end mt-3 mt-md-0">
                <a href="{{ route('sic.novo-pedido') }}" class="btn btn-primary rounded-3 fw-bold shadow-sm">
                    <i class="fas fa-plus me-2"></i>Novo Pedido
                </a>
            </div>
        </div>

        {{-- Card do usuário logado --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <h5 class="fw-bold mb-1">
                            <i class="fas fa-user-circle text-primary me-2"></i>
                            {{ session('sic_user_name') }}
                        </h5>
                        <p class="text-muted mb-0">
                            Consulte o andamento das suas solicitações e acompanhe as respostas enviadas pelo órgão.
                        </p>
                    </div>

                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('sic.index') }}" class="btn btn-outline-secondary rounded-3 fw-bold">
                            <i class="fas fa-home me-2"></i>Início
                        </a>

                        <a href="{{ route('sic.novo-pedido') }}" class="btn btn-primary rounded-3 fw-bold">
                            <i class="fas fa-plus me-2"></i>Novo Pedido
                        </a>

                        <form action="{{ route('sic.logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger rounded-3 fw-bold">
                                <i class="fas fa-sign-out-alt me-2"></i>Sair
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Resumo --}}
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div class="icon-square bg-soft-blue text-primary me-3">
                                <i class="fas fa-folder-open"></i>
                            </div>
                            <div>
                                <div class="text-muted small">Total de pedidos</div>
                                <div class="fs-3 fw-bold">{{ $pedidos->count() }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @php
                $abertos = $pedidos->whereIn('status', ['A', 'ABERTO', 'EM ANÁLISE', 'PENDENTE'])->count();
                $respondidos = $pedidos->whereIn('status', ['R', 'RESPONDIDO', 'CONCLUÍDO'])->count();
            @endphp

            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div class="icon-square bg-soft-warning text-warning me-3">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div>
                                <div class="text-muted small">Em andamento</div>
                                <div class="fs-3 fw-bold">{{ $abertos }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div class="icon-square bg-soft-green text-success me-3">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div>
                                <div class="text-muted small">Respondidos</div>
                                <div class="fs-3 fw-bold">{{ $respondidos }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Lista de pedidos --}}
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-0">
                <div class="p-4 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h5 class="fw-bold mb-1">Solicitações registradas</h5>
                        <p class="text-muted small mb-0">Veja abaixo o histórico dos pedidos cadastrados em seu nome.</p>
                    </div>
                </div>

                @if ($pedidos->count())
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="bg-light">
                                <tr class="text-muted small text-uppercase">
                                    <th class="ps-4">Protocolo</th>
                                    <th>Assunto</th>
                                    <th>Data</th>
                                    <th>Status</th>
                                    <th class="text-end pe-4">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pedidos as $pedido)
                                    <tr>
                                        {{-- Protocolo: assumindo que seja o ID ou campo protocolo se existir --}}
                                        <td class="ps-4 fw-semibold">
                                            {{ $pedido->id }}
                                        </td>

                                        {{-- Assunto/Título --}}
                                        <td>
                                            <div class="fw-semibold text-dark">
                                                {{ $pedido->titulo ?? 'Sem título' }}
                                            </div>
                                        </td>

                                        {{-- Data --}}
                                        <td class="text-muted">
                                            {{ \Carbon\Carbon::parse($pedido->datahora)->format('d/m/Y H:i') }}
                                        </td>

                                        {{-- Status --}}
                                        <td>
                                            @php
                                                // Normaliza o status para comparação
                                                $status = strtoupper(trim($pedido->situacao ?? 'A'));

                                                // Lógica de cores baseada no campo 'situacao'
                                                $isConcluido = in_array($status, [
                                                    'R',
                                                    'RESPONDIDO',
                                                    'CONCLUÍDO',
                                                    'CONCLUIDO',
                                                ]);
                                                $isCancelado = $status === 'CANCELADO';

                                                $statusClass = $isConcluido
                                                    ? 'bg-success-subtle text-success border-success'
                                                    : ($isCancelado
                                                        ? 'bg-danger-subtle text-danger border-danger'
                                                        : 'bg-warning-subtle text-warning border-warning');
                                            @endphp

                                            <span class="badge rounded-pill px-3 py-2 border {{ $statusClass }}">
                                                {{ $status }}
                                            </span>
                                        </td>

                                        {{-- Ações --}}
                                        <td class="text-end pe-4">
                                            <a href="{{ route('sic.pedido.show', $pedido->id) }}"
                                                class="btn btn-sm btn-outline-primary rounded-3">
                                                <i class="fas fa-eye me-1"></i>Visualizar
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-5 text-center">
                        <div class="mb-3">
                            <div class="icon-empty mx-auto">
                                <i class="fas fa-inbox"></i>
                            </div>
                        </div>
                        <h5 class="fw-bold">Nenhum pedido encontrado</h5>
                        <p class="text-muted mb-4">
                            Você ainda não registrou solicitações no SIC.
                        </p>
                        <a href="{{ route('sic.novo-pedido') }}" class="btn btn-primary rounded-3 fw-bold px-4">
                            <i class="fas fa-plus me-2"></i>Registrar meu primeiro pedido
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <style>
        .bg-light-gray {
            background-color: #f8f9fa;
        }

        .bg-soft-blue {
            background-color: #eef4ff;
        }

        .bg-soft-green {
            background-color: #f0fff4;
        }

        .bg-soft-warning {
            background-color: #fff8e1;
        }

        .rounded-4 {
            border-radius: 1rem !important;
        }

        .icon-square {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            font-size: 1.15rem;
        }

        .icon-empty {
            width: 72px;
            height: 72px;
            background: #eef4ff;
            color: #0d6efd;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
        }

        .table> :not(caption)>*>* {
            padding-top: 1rem;
            padding-bottom: 1rem;
            vertical-align: middle;
        }
    </style>
@endsection
