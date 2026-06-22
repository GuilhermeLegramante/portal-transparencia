@extends('layouts.app')

@section('content')
    <div class="container-fluid px-lg-5 py-4 bg-light-gray min-vh-100">
        <x-breadcrumb :items="$breadcrumb" />

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

        <div class="row mb-5 mt-3">
            <div class="col-md-8">
                <h1 class="fw-bold text-dark border-start border-primary border-5 ps-3">SIC</h1>
                <p class="text-muted fs-5">Serviço de Informação ao Cidadão - {{ config('app.client_full_name') }}</p>
            </div>
            <div class="col-md-4 text-md-end d-flex align-items-center justify-content-md-end">
                <span class="badge bg-white text-primary border border-primary px-3 py-2 rounded-pill shadow-sm">
                    <i class="fas fa-landmark me-2"></i>Acesso à Informação
                </span>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden card-hover">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="icon-square bg-soft-blue text-primary me-3">
                                        <i class="fas fa-plus"></i>
                                    </div>
                                    <h5 class="fw-bold mb-0">Novo Pedido</h5>
                                </div>
                                <p class="text-muted small">Ainda não possui cadastro? Registre-se para solicitar
                                    informações formais.</p>
                                <a href="{{ route('sic.cadastro') }}" class="btn btn-primary w-100 rounded-3 fw-bold">Criar
                                    Cadastro</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden card-hover">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="icon-square bg-soft-green text-success me-3">
                                        <i class="fas fa-sign-in-alt"></i>
                                    </div>
                                    <h5 class="fw-bold mb-0">Entrar no Sistema</h5>
                                </div>
                                <p class="text-muted small">Já é cadastrado? Acesse seu perfil para acompanhar seus pedidos.
                                </p>
                                <a href="{{ route('sic.login') }}"
                                    class="btn btn-outline-dark w-100 rounded-3 fw-bold">Fazer Login</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-4 border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4 text-dark"><i class="fas fa-book-reader me-2 text-primary"></i> Guia Rápido
                            de Utilização</h5>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="p-3 border rounded-3 bg-white text-center h-100">
                                    <div class="h3 fw-bold text-light-gray mb-1">01</div>
                                    <h6 class="fw-bold">Cadastro</h6>
                                    <p class="small text-muted mb-0">Identifique-se de forma segura.</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 border rounded-3 bg-white text-center h-100">
                                    <div class="h3 fw-bold text-light-gray mb-1">02</div>
                                    <h6 class="fw-bold">Solicitação</h6>
                                    <p class="small text-muted mb-0">Envie sua pergunta ou pedido.</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 border rounded-3 bg-white text-center h-100">
                                    <div class="h3 fw-bold text-light-gray mb-1">03</div>
                                    <h6 class="fw-bold">Resposta</h6>
                                    <p class="small text-muted mb-0">Receba no prazo legal.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3 border-bottom pb-2">Unidade de Atendimento</h6>
                        <div class="mb-3 d-flex">
                            <i class="fas fa-map-marker-alt text-primary me-3 mt-1"></i>
                            <span class="small">{{ config('app.client_address') }}</span>
                        </div>
                        <div class="mb-3 d-flex">
                            <i class="fas fa-clock text-primary me-3 mt-1"></i>
                            <span class="small">{{ config('app.client_operation_hours') }}</span>
                        </div>
                        <div class="d-flex">
                            <i class="fas fa-envelope text-primary me-3 mt-1"></i>
                            <span class="small">{{ config('app.client_email') }}</span>
                        </div>
                    </div>
                </div>

                <a href="{{ route('sic.estatisticas') }}"
                    class="btn btn-white w-100 shadow-sm rounded-3 py-3 mb-2 border text-dark fw-bold">
                    <i class="fas fa-chart-line me-2 text-primary"></i> Estatísticas de Pedidos
                </a>
                <a href="{{ route('sic.contato') }}"
                    class="btn btn-white w-100 shadow-sm rounded-3 py-3 border text-dark fw-bold">
                    <i class="fas fa-question-circle me-2 text-primary"></i> Dúvidas e Orientações
                </a>
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

        .text-light-gray {
            color: #dee2e6;
        }

        .rounded-4 {
            border-radius: 1rem !important;
        }

        .icon-square {
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            font-size: 1.2rem;
        }

        .card-hover {
            transition: all 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.08) !important;
        }

        .btn-white {
            background-color: #fff;
            border: 1px solid #eee;
        }

        .btn-white:hover {
            background-color: #f8f9fa;
            border-color: #ddd;
        }
    </style>
@endsection
