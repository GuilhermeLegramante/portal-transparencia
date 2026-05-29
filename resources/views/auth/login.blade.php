@extends('layouts.app')

@section('content')
    <div class="container my-5 animate__animated animate__fadeIn">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card border-0 shadow-sm rounded-3 overflow-hidden mt-5">
                    <div class="card-body p-5">

                        {{-- Cabeçalho do Login --}}
                        <div class="text-center mb-4">
                            @php
                                $client = config('app.client_name', 'default');
                            @endphp
                            <img src="{{ asset('img/' . $client . '.png') }}" alt="Logo" style="height: 60px;"
                                class="mb-3">
                            <h4 class="fw-bold text-dark">Acesso Restrito</h4>
                            <p class="text-muted small">Insira sua senha para acessar o painel administrativo</p>
                        </div>

                        {{-- Exibição de Erros Globais --}}
                        @if ($errors->has('login_error'))
                            <div class="alert alert-danger border-0 shadow-sm small d-flex align-items-center gap-2 mb-4">
                                <i class="fas fa-exclamation-circle"></i>
                                <div>{{ $errors->first('login_error') }}</div>
                            </div>
                        @endif

                        <form action="{{ route('login.submit') }}" method="POST">
                            @csrf

                            {{-- Campo Identificador do Cliente (Bloqueado/Automático) --}}
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-secondary">Cliente / Portal</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0 text-muted">
                                        <i class="fas fa-building small"></i>
                                    </span>
                                    <input type="text" class="form-control bg-light border-0 fw-bold"
                                        value="{{ strtoupper(config('app.client_name')) }}" readonly tabindex="-1">
                                </div>
                            </div>

                            {{-- Campo da Senha --}}
                            <div class="mb-4">
                                <label for="senha" class="form-label small fw-bold text-secondary">Senha de
                                    Acesso</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0 text-muted">
                                        <i class="fas fa-lock small"></i>
                                    </span>
                                    <input type="password" name="senha" id="senha"
                                        class="form-control bg-light border-0 @error('senha') is-invalid @enderror"
                                        placeholder="Digite sua senha administrativa" required autofocus>

                                    @error('senha')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Botão de Enviar --}}
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg rounded-3 fw-bold shadow-sm py-2 fs-6">
                                    <i class="fas fa-sign-in-alt me-2"></i>Entrar no Sistema
                                </button>
                            </div>
                        </form>

                    </div>
                </div>

                <div class="text-center mt-4">
                    <a href="{{ route('home') }}" class="text-decoration-none small text-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Voltar para o Início
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
