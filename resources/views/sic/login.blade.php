@extends('layouts.app')

@section('content')
    <div class="container py-5 min-vh-100 d-flex align-items-center bg-light-gray">
        <div class="row justify-content-center w-100">
            <div class="col-md-5">
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden p-4">
                    <div class="card-body">
                        <div class="text-center mb-5">
                            <div class="bg-primary d-inline-block p-3 rounded-circle mb-3 shadow-sm">
                                <i class="fas fa-lock text-white fa-2x"></i>
                            </div>
                            <h3 class="fw-bold text-dark">Acesso ao SIC</h3>
                            <p class="text-muted">Portal da Transparência de {{ config('app.client_full_name') }}</p>
                        </div>

                        {{-- Mensagem de sucesso --}}
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4"
                                role="alert">
                                <i class="fas fa-check-circle me-2"></i>
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Fechar"></button>
                            </div>
                        @endif

                        {{-- Mensagem de erro via sessão --}}
                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4"
                                role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Fechar"></button>
                            </div>
                        @endif

                        {{-- Erros de validação / autenticação --}}
                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4"
                                role="alert">
                                <div class="fw-bold mb-2">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Não foi possível entrar no sistema.
                                </div>
                                <ul class="mb-0 ps-3">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Fechar"></button>
                            </div>
                        @endif

                        <form action="{{ route('sic.auth') }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label class="small fw-bold text-muted mb-2">E-MAIL</label>
                                <input type="email" name="email"
                                    class="form-control form-control-lg border-0 bg-light-gray rounded-3 fs-6"
                                    placeholder="nome@exemplo.com">
                            </div>
                            <div class="mb-4">
                                <label class="small fw-bold text-muted mb-2">SENHA</label>
                                <input type="password" name="password"
                                    class="form-control form-control-lg border-0 bg-light-gray rounded-3 fs-6"
                                    placeholder="Digite sua senha">
                            </div>

                            <button type="submit"
                                class="btn btn-primary btn-lg w-100 rounded-pill py-3 fw-bold shadow-sm mb-4">
                                Entrar no Portal
                            </button>
                        </form>

                        <div class="text-center">
                            <p class="text-muted small">Não possui uma conta? <a href="{{ route('sic.cadastro') }}"
                                    class="text-primary fw-bold text-decoration-none">Cadastre-se aqui</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
