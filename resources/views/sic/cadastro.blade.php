@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-7">
                <div class="card shadow border-0 animate__animated animate__fadeIn">
                    <div class="card-body p-5">
                        <h3 class="fw-bold mb-4 text-center">Cadastro de Cidadão</h3>

                        {{-- Erros gerais --}}
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <strong>Não foi possível concluir o cadastro.</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif


                        <form action="{{ route('sic.registrar') }}" method="POST">
                            @csrf
                            {{-- O idcliente é passado oculto ou tratado no Controller via config --}}
                            <input type="hidden" name="idcliente" value="{{ config('app.client_id') }}">

                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">Nome Completo / Razão Social</label>
                                    <input type="text" name="name" class="form-control" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Tipo de Pessoa</label>
                                    <select name="tipopessoa" class="form-select" required>
                                        <option value="F">Física</option>
                                        <option value="J">Jurídica</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">CPF / CNPJ</label>
                                    <input type="text" name="documento" class="form-control" placeholder="000.000.000-00"
                                        required>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label fw-bold">E-mail (Será seu usuário)</label>
                                    <input type="email" name="email" class="form-control" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Senha</label>
                                    <input type="password" name="password" class="form-control" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Confirmar Senha</label>
                                    <input type="password" name="password_confirmation" class="form-control" required>
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg shadow-sm">Finalizar Cadastro</button>
                                <a href="{{ route('sic.login') }}" class="text-center text-muted text-decoration-none">Já
                                    possuo conta, quero entrar</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
