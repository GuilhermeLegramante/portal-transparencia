@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card shadow border-0 animate__animated animate__fadeIn">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <i class="fas fa-user-circle fa-4x text-primary mb-3"></i>
                            <h3 class="fw-bold">Acesso ao SIC</h3>
                            <p class="text-muted">Portal da Transparência - {{ config('app.client_full_name') }}</p>
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-danger border-0">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('login') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-bold">E-mail</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i
                                            class="fas fa-envelope text-muted"></i></span>
                                    <input type="email" name="email" class="form-control" placeholder="seu@email.com"
                                        required autofocus>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Senha</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="fas fa-lock text-muted"></i></span>
                                    <input type="password" name="password" class="form-control" placeholder="******"
                                        required>
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg shadow-sm">Entrar</button>
                                <a href="{{ route('sic.cadastro') }}" class="btn btn-outline-secondary">Ainda não tenho
                                    cadastro</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
