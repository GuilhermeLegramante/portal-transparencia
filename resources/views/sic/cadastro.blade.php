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
                                    <input type="text" name="name" class="form-control" value="{{ old('name') }}"
                                        required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Tipo de Pessoa</label>
                                    <select name="tipopessoa" id="tipopessoa" class="form-select" required>
                                        <option value="F" {{ old('tipopessoa', 'F') == 'F' ? 'selected' : '' }}>Física
                                        </option>
                                        <option value="J" {{ old('tipopessoa') == 'J' ? 'selected' : '' }}>Jurídica
                                        </option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">CPF / CNPJ</label>
                                    <input type="text" name="documento" id="documento" class="form-control"
                                        value="{{ old('documento') }}" placeholder="000.000.000-00" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Telefone</label>
                                    <input type="text" name="telefone" id="telefone" class="form-control"
                                        value="{{ old('telefone') }}" placeholder="(55) 99999-9999" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold">E-mail (Será seu usuário)</label>
                                    <input type="email" name="email" class="form-control" value="{{ old('email') }}"
                                        required>
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
                                <button type="submit" class="btn btn-primary btn-lg shadow-sm">
                                    Finalizar Cadastro
                                </button>

                                <a href="{{ route('sic.login') }}" class="text-center text-muted text-decoration-none">
                                    Já possuo conta, quero entrar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tipoPessoa = document.getElementById('tipopessoa');
            const documento = document.getElementById('documento');
            const telefone = document.getElementById('telefone');

            function onlyNumbers(value) {
                return value.replace(/\D/g, '');
            }

            function maskCPF(value) {
                value = onlyNumbers(value).slice(0, 11);
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
                return value;
            }

            function maskCNPJ(value) {
                value = onlyNumbers(value).slice(0, 14);
                value = value.replace(/^(\d{2})(\d)/, '$1.$2');
                value = value.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
                value = value.replace(/\.(\d{3})(\d)/, '.$1/$2');
                value = value.replace(/(\d{4})(\d)/, '$1-$2');
                return value;
            }

            function applyDocumentoMask() {
                const tipo = tipoPessoa.value;
                if (tipo === 'J') {
                    documento.value = maskCNPJ(documento.value);
                    documento.placeholder = '00.000.000/0000-00';
                } else {
                    documento.value = maskCPF(documento.value);
                    documento.placeholder = '000.000.000-00';
                }
            }

            function maskTelefone(value) {
                value = onlyNumbers(value).slice(0, 11);

                if (value.length <= 10) {
                    // (55) 3333-4444
                    value = value.replace(/^(\d{2})(\d)/, '($1) $2');
                    value = value.replace(/(\d{4})(\d)/, '$1-$2');
                } else {
                    // (55) 99999-9999
                    value = value.replace(/^(\d{2})(\d)/, '($1) $2');
                    value = value.replace(/(\d{5})(\d)/, '$1-$2');
                }

                return value;
            }

            tipoPessoa.addEventListener('change', applyDocumentoMask);

            documento.addEventListener('input', function() {
                applyDocumentoMask();
            });

            telefone.addEventListener('input', function() {
                telefone.value = maskTelefone(telefone.value);
            });

            // aplica máscaras no carregamento caso tenha old()
            applyDocumentoMask();
            telefone.value = maskTelefone(telefone.value);
        });
    </script>
@endsection
