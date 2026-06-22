@extends('layouts.app')

@section('content')
    <div class="container-fluid px-lg-5 py-4 bg-light-gray min-vh-100">
        <x-breadcrumb :items="$breadcrumb" />

        {{-- Cabeçalho --}}
        <div class="row mb-4 mt-3">
            <div class="col-md-8">
                <h1 class="fw-bold text-dark border-start border-primary border-5 ps-3">Novo Pedido</h1>
                <p class="text-muted fs-5 mb-0">
                    Registre uma nova solicitação no Serviço de Informação ao Cidadão - {{ config('app.client_full_name') }}
                </p>
            </div>
        </div>

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

        {{-- Card de formulário --}}
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <form action="{{ route('sic.pedido.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row g-3">
                        {{-- Título do pedido --}}
                        <div class="col-12">
                            <label for="titulo" class="form-label fw-bold">Assunto/Título da Solicitação</label>
                            <input type="text" class="form-control rounded-3 @error('titulo') is-invalid @enderror"
                                id="titulo" name="titulo" value="{{ old('titulo') }}" required
                                placeholder="Resuma o assunto do seu pedido">
                            @error('titulo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Descrição completa --}}
                        <div class="col-12">
                            <label for="descricao" class="form-label fw-bold">Descrição detalhada</label>
                            <textarea class="form-control rounded-3 @error('descricao') is-invalid @enderror" id="descricao" name="descricao"
                                rows="6" required placeholder="Descreva aqui sua solicitação de forma detalhada">{{ old('descricao') }}</textarea>
                            @error('descricao')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Campo de Anexo (Opcional) --}}
                        <div class="col-md-6">
                            <label for="arquivo" class="form-label fw-bold">Anexar documento (opcional)</label>
                            <input type="file" class="form-control rounded-3" id="arquivo" name="arquivo">
                            <div class="form-text small">Formatos aceitos: PDF, JPG, PNG (máx. 5MB)</div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('sic.pedidos') }}" class="btn btn-outline-secondary rounded-3 px-4">
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary rounded-3 px-4 fw-bold shadow-sm">
                            <i class="fas fa-paper-plane me-2"></i>Enviar Solicitação
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Reaproveitamento do estilo que você já definiu na outra página --}}
    <style>
        .bg-light-gray {
            background-color: #f8f9fa;
        }

        .rounded-4 {
            border-radius: 1rem !important;
        }

        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
        }
    </style>
@endsection
