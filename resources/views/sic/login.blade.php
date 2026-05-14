@extends('layouts.app')

@section('content')
    <div class="container-fluid p-0 overflow-hidden">
        <div class="row g-0 min-vh-100">
            <div
                class="col-lg-6 d-none d-lg-flex bg-gradient-primary align-items-center justify-content-center p-5 text-white">
                <div class="max-w-400 text-center">
                    <i class="fas fa-shield-alt fa-5x mb-4"></i>
                    <h1 class="display-4 fw-bold">Ambiente Seguro</h1>
                    <p class="fs-5 opacity-75">Suas informações estão protegidas. Use sua conta para interagir com o portal
                        da prefeitura de {{ config('app.client_full_name') }}.</p>
                </div>
            </div>

            <div class="col-lg-6 d-flex align-items-center justify-content-center p-5 bg-white">
                <div class="w-100 shadow-none p-lg-5" style="max-width: 450px;">
                    <div class="text-center mb-5">
                        <h2 class="fw-bold">Acesse sua conta</h2>
                        <p class="text-muted">Bem-vindo de volta!</p>
                    </div>

                    <form action="{{ route('sic.auth') }}" method="POST">
                        @csrf
                        <div class="form-floating mb-3">
                            <input type="email" name="email" class="form-control rounded-3" id="email"
                                placeholder="nome@exemplo.com">
                            <label for="email">Endereço de E-mail</label>
                        </div>
                        <div class="form-floating mb-4">
                            <input type="password" name="password" class="form-control rounded-3" id="password"
                                placeholder="Senha">
                            <label for="password">Sua Senha</label>
                        </div>

                        <button class="btn btn-primary btn-lg w-100 rounded-pill py-3 shadow-sm mb-4" type="submit">
                            Entrar no Sistema
                        </button>

                        <div class="text-center">
                            <span class="text-muted small">Não tem uma conta?</span>
                            <a href="{{ route('sic.cadastro') }}"
                                class="text-primary fw-bold text-decoration-none ms-1 small">Criar cadastro agora</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
