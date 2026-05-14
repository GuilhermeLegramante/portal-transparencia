@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <x-breadcrumb :items="$breadcrumb" />

        <div class="row justify-content-center animate__animated animate__fadeIn">
            <div class="col-md-10">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white p-3 border-bottom">
                        <h4 class="fw-bold mb-0 text-primary">
                            <i class="fas fa-envelope-open-text me-2"></i>Fale Conosco - SIC
                        </h4>
                    </div>
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-md-7 border-end">
                                <p class="text-muted mb-4">
                                    utilize o formulário abaixo para dúvidas rápidas, orientações sobre como utilizar o
                                    sistema ou informações gerais que não exijam abertura de processo formal.
                                </p>

                                @if (session('success'))
                                    <div class="alert alert-success border-0 shadow-sm">
                                        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                                    </div>
                                @endif

                                <form action="{{ route('sic.enviar') }}" method="POST">
                                    @csrf
                                    <div class="row g-3">
                                        <div class="col-md-12">
                                            <label class="form-label fw-bold">Nome Completo</label>
                                            <input type="text" name="nome" class="form-control"
                                                placeholder="Ex: João Silva" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Seu E-mail</label>
                                            <input type="email" name="email" class="form-control"
                                                placeholder="joao@email.com" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">Telefone/WhatsApp</label>
                                            <input type="text" name="telefone" class="form-control"
                                                placeholder="(00) 00000-0000">
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label fw-bold">Assunto</label>
                                            <select name="assunto" class="form-select" required>
                                                <option value="">Selecione...</option>
                                                <option value="Dúvida sobre a LAI">Dúvida sobre a LAI</option>
                                                <option value="Dificuldade no Cadastro">Dificuldade no Cadastro</option>
                                                <option value="Reclamação">Reclamação</option>
                                                <option value="Outros">Outros</option>
                                            </select>
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label fw-bold">Sua Mensagem</label>
                                            <textarea name="mensagem" class="form-control" rows="5" placeholder="Descreva sua dúvida detalhadamente..."
                                                required></textarea>
                                        </div>
                                        <div class="col-md-12 text-end">
                                            <button type="submit" class="btn btn-primary btn-lg px-5 shadow-sm">
                                                <i class="fas fa-paper-plane me-2"></i> Enviar Mensagem
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div class="col-md-5 ps-md-4">
                                <h5 class="fw-bold mb-4">Informações de Atendimento</h5>

                                <div class="d-flex mb-4">
                                    <div class="icon-box bg-light-primary rounded p-3 me-3">
                                        <i class="fas fa-map-marked-alt text-primary fa-lg"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0">Endereço</h6>
                                        <small class="text-muted">{{ config('app.client_address') }}</small>
                                    </div>
                                </div>

                                <div class="d-flex mb-4">
                                    <div class="icon-box bg-light-success rounded p-3 me-3">
                                        <i class="fas fa-clock text-success fa-lg"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0">Horário de Funcionamento</h6>
                                        <small class="text-muted">{{ config('app.client_operation_hours') }}</small>
                                    </div>
                                </div>

                                <div class="d-flex mb-4">
                                    <div class="icon-box bg-light-info rounded p-3 me-3">
                                        <i class="fas fa-phone-alt text-info fa-lg"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0">Telefone</h6>
                                        <small class="text-muted">{{ config('app.phone') }}</small>
                                    </div>
                                </div>

                                <div class="alert alert-warning border-0 small">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Importante:</strong> Este canal não gera protocolo oficial de pedido de
                                    informação. Para solicitar dados formais da prefeitura, utilize o link <strong>"Novo
                                        Pedido"</strong> na página principal do SIC.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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

        .bg-light-info {
            background-color: #e1f5fe;
        }

        .icon-box {
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
@endsection
