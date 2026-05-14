@extends('layouts.app')

@section('content')
    <div class="container-fluid px-lg-5">
        <x-breadcrumb :items="$breadcrumb" />

        <div class="row mb-5">
            <div class="col-12">
                <div class="card border-0 rounded-4 overflow-hidden shadow-lg bg-gradient-primary text-white">
                    <div class="card-body p-5 d-flex align-items-center position-relative">
                        <div class="flex-grow-1">
                            <h1 class="display-5 fw-bold mb-3">Serviço de Informação ao Cidadão</h1>
                            <p class="fs-5 opacity-75 mb-0 max-w-600">
                                Garantindo o seu direito constitucional de acesso à informação pública de forma rápida e
                                transparente.
                            </p>
                        </div>
                        <div class="d-none d-lg-block position-absolute end-0 bottom-0 opacity-25 p-4">
                            <i class="fas fa-bullhorn fa-10x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-sm rounded-4 hover-lift">
                            <div class="card-body p-4 text-center">
                                <div class="icon-circle bg-soft-primary text-primary mx-auto mb-3">
                                    <i class="fas fa-user-plus"></i>
                                </div>
                                <h4 class="fw-bold">Cadastro</h4>
                                <p class="text-muted small">Primeiro passo para quem deseja fazer uma solicitação formal.
                                </p>
                                <a href="{{ route('sic.cadastro') }}"
                                    class="btn btn-outline-primary rounded-pill px-4">Cadastrar-se</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100 border-0 shadow-sm rounded-4 hover-lift">
                            <div class="card-body p-4 text-center text-white bg-primary">
                                <div class="icon-circle bg-white-soft text-white mx-auto mb-3">
                                    <i class="fas fa-key"></i>
                                </div>
                                <h4 class="fw-bold">Acesso</h4>
                                <p class="small opacity-75">Já possui conta? Acesse para pedir informações ou acompanhar
                                    status.</p>
                                <a href="{{ route('sic.login') }}" class="btn btn-light rounded-pill px-4 fw-bold">Fazer
                                    Login</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mt-4 border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4 border-bottom pb-2 text-secondary">
                            <i class="fas fa-question-circle me-2"></i>Como realizar um pedido?
                        </h5>
                        <div class="d-flex align-items-start mb-3">
                            <span class="badge bg-primary rounded-circle p-2 me-3 mt-1">01</span>
                            <p class="mb-0"><strong>Autenticação:</strong> Faça seu login ou crie uma conta no portal.</p>
                        </div>
                        <div class="d-flex align-items-start mb-3">
                            <span class="badge bg-primary rounded-circle p-2 me-3 mt-1">02</span>
                            <p class="mb-0"><strong>Formulário:</strong> Descreva de forma clara e objetiva a informação
                                desejada.</p>
                        </div>
                        <div class="d-flex align-items-start">
                            <span class="badge bg-primary rounded-circle p-2 me-3 mt-1">03</span>
                            <p class="mb-0"><strong>Protocolo:</strong> Guarde o seu número para acompanhar o prazo legal
                                de resposta.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white p-4 border-0 pb-0">
                        <h5 class="fw-bold mb-0">Atendimento Presencial</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="info-item mb-4">
                            <label class="text-uppercase small text-muted fw-bold d-block mb-1">Localização</label>
                            <div class="d-flex">
                                <i class="fas fa-map-marker-alt text-danger me-3 mt-1"></i>
                                <span>{{ config('app.client_address') }}</span>
                            </div>
                        </div>
                        <div class="info-item mb-4">
                            <label class="text-uppercase small text-muted fw-bold d-block mb-1">E-mail</label>
                            <div class="d-flex">
                                <i class="fas fa-envelope text-primary me-3 mt-1"></i>
                                <span>{{ config('app.client_email') }}</span>
                            </div>
                        </div>
                        <div class="info-item mb-4">
                            <label class="text-uppercase small text-muted fw-bold d-block mb-1">Funcionamento</label>
                            <div class="d-flex">
                                <i class="fas fa-clock text-success me-3 mt-1"></i>
                                <span>{{ config('app.client_operation_hours') }}</span>
                            </div>
                        </div>
                        <hr>
                        <a href="{{ route('sic.estatisticas') }}" class="btn btn-soft-dark w-100 rounded-pill mb-2 mt-3">
                            <i class="fas fa-chart-pie me-2"></i>Ver Estatísticas
                        </a>
                        <a href="{{ route('sic.contato') }}" class="btn btn-soft-primary w-100 rounded-pill">
                            <i class="fas fa-comments me-2"></i>Dúvidas Rápidas
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .bg-gradient-primary {
            background: linear-gradient(135deg, #0d6efd 0%, #0046b8 100%);
        }

        .bg-soft-primary {
            background-color: #e7f1ff;
        }

        .bg-white-soft {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .btn-soft-primary {
            background-color: #e7f1ff;
            color: #0d6efd;
            font-weight: bold;
        }

        .btn-soft-primary:hover {
            background-color: #0d6efd;
            color: #fff;
        }

        .btn-soft-dark {
            background-color: #f1f1f1;
            color: #333;
            font-weight: bold;
        }

        .rounded-4 {
            border-radius: 1rem !important;
        }

        .max-w-600 {
            max-width: 600px;
        }

        .icon-circle {
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-size: 1.5rem;
        }

        .hover-lift {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            cursor: pointer;
        }

        .hover-lift:hover {
            transform: translateY(-5px);
            box-shadow: 0 1rem 3rem rgba(0, 0, 0, .1) !important;
        }
    </style>
@endsection
