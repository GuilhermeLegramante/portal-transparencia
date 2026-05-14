@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <x-breadcrumb :items="$breadcrumb" />

        <div class="row mb-4 animate__animated animate__fadeIn">
            <div class="col-md-8">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body p-4">
                        <h2 class="fw-bold text-primary mb-3">
                            <i class="fas fa-info-circle me-2"></i>Sobre o SIC
                        </h2>
                        <p class="lead">O Serviço de Informações ao Cidadão (SIC) permite que qualquer pessoa, física ou
                            jurídica, encaminhe pedidos de acesso à informação.</p>
                        <p>Por meio da Lei de Acesso à Informação (Lei nº 12.527/2011), garantimos o seu direito de receber
                            dos órgãos públicos informações de seu interesse particular, ou de interesse coletivo ou geral.
                        </p>

                        <div class="alert alert-info border-0 shadow-sm mt-4">
                            <h5 class="fw-bold"><i class="fas fa-question-circle me-2"></i>Como funciona?</h5>
                            <ol class="mb-0">
                                <li><strong>Cadastre-se:</strong> Crie seu perfil de cidadão para ter acesso à área
                                    restrita.</li>
                                <li><strong>Faça seu Pedido:</strong> Preencha o formulário detalhando a informação
                                    desejada.</li>
                                <li><strong>Acompanhe:</strong> Receba um número de protocolo para seguir o status da sua
                                    demanda.</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card bg-primary text-white shadow-sm border-0 mb-3">
                    <div class="card-body text-center p-4">
                        <i class="fas fa-user-plus fa-3x mb-3"></i>
                        <h4 class="fw-bold">Novo Pedido</h4>
                        <p>Ainda não possui cadastro? Registre-se para solicitar informações.</p>
                        <a href="{{ route('sic.login') }}" class="btn btn-light btn-lg w-100 fw-bold">Acessar /
                            Cadastrar</a>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white fw-bold">Informações da Unidade</div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2"><i class="fas fa-map-marker-alt text-primary me-2"></i>
                                {{ config('app.client_address') }}</li>
                            <li class="mb-2"><i class="fas fa-clock text-primary me-2"></i>
                                {{ config('app.client_operation_hours') }}</li>
                            <li class="mb-2"><i class="fas fa-phone text-primary me-2"></i> {{ config('app.phone') }}</li>
                            <li><i class="fas fa-envelope text-primary me-2"></i> {{ config('app.client_email') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-md-4">
                <a href="{{ route('sic.estatisticas') }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 hover-shadow transition-all">
                        <div class="card-body d-flex align-items-center">
                            <div class="icon-box bg-light-primary rounded p-3 me-3">
                                <i class="fas fa-chart-bar fa-2x text-primary"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold text-dark">Estatísticas</h5>
                                <small class="text-muted">Consulta de pedidos por exercício</small>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-4">
                <a href="{{ route('sic.contato') }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 hover-shadow transition-all">
                        <div class="card-body d-flex align-items-center">
                            <div class="icon-box bg-light-success rounded p-3 me-3">
                                <i class="fas fa-envelope-open-text fa-2x text-success"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold text-dark">Fale Conosco</h5>
                                <small class="text-muted">Dúvidas rápidas via e-mail</small>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-4">
                <a href="{{ route('ajuda.faq') }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 hover-shadow transition-all">
                        <div class="card-body d-flex align-items-center">
                            <div class="icon-box bg-light-warning rounded p-3 me-3">
                                <i class="fas fa-question fa-2x text-warning"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold text-dark">Perguntas Frequentes</h5>
                                <small class="text-muted">Dúvidas comuns sobre a LAI</small>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <style>
        .bg-light-primary {
            background-color: #e7f1ff;
        }

        .bg-light-success {
            background-color: #e8fadf;
        }

        .bg-light-warning {
            background-color: #fff8e6;
        }

        .hover-shadow:hover {
            transform: translateY(-5px);
            box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
        }

        .transition-all {
            transition: all 0.3s ease;
        }
    </style>
@endsection
