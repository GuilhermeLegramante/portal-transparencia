@extends('layouts.app')

@section('content')
    <div class="container-fluid px-lg-5 py-4 bg-light-gray min-vh-100">
        <x-breadcrumb :items="$breadcrumb" />

        <div class="row justify-content-center">
            <div class="col-xl-8 col-lg-10">

                {{-- Mensagens de Alerta Legadas --}}
                @if (session('error'))
                    <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-3">
                        <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
                    </div>
                @endif

                <div class="card shadow-sm border-0 rounded-3 bg-white">
                    <div class="card-header bg-white border-0 pt-4 px-4 d-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="fw-bold text-dark mb-1">Novo Cadastro de Publicação</h4>
                            <p class="text-muted small mb-0">Insira documentos e relatórios oficiais no Portal da
                                Transparência.</p>
                        </div>
                        <a href="{{ route('publicacoes.index') }}" class="btn btn-sm btn-outline-secondary rounded-3">
                            <i class="fas fa-arrow-left me-1"></i> Voltar
                        </a>
                    </div>

                    <div class="card-body p-4">
                        <form method="POST" action="{{ route('publicacoes.store') }}" enctype="multipart/form-data">
                            @csrf

                            <div class="row g-4">
                                {{-- Tipo de Publicação --}}
                                <div class="col-md-6">
                                    <label class="small fw-bold text-muted text-uppercase mb-2 d-block">Tipo de
                                        Publicação</label>
                                    <select name="tipo_publicacao" id="tipo_publicacao"
                                        class="form-select border-gray rounded-3 @error('tipo_publicacao') is-invalid @enderror">
                                        <option value="geral" {{ old('tipo_publicacao') == 'geral' ? 'selected' : '' }}>
                                            Publicação Geral (Usa Tags)</option>
                                        <option value="prestacao"
                                            {{ old('tipo_publicacao') == 'prestacao' ? 'selected' : '' }}>Prestação de
                                            Contas (Categoria Fixa)</option>
                                    </select>
                                    @error('tipo_publicacao')
                                        <div class="invalid-feedback fw-bold">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Exercício --}}
                                <div class="col-md-3">
                                    <label class="small fw-bold text-muted text-uppercase mb-2 d-block">Exercício /
                                        Ano</label>
                                    <input type="number" name="exercicio"
                                        class="form-control border-gray rounded-3 @error('exercicio') is-invalid @enderror"
                                        value="{{ old('exercicio', date('Y')) }}" min="2000" max="{{ date('Y') + 1 }}">
                                    @error('exercicio')
                                        <div class="invalid-feedback fw-bold">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Data e Hora da Publicação --}}
                                <div class="col-md-3">
                                    <label class="small fw-bold text-muted text-uppercase mb-2 d-block">Data/Hora
                                        Publicação</label>
                                    <input type="datetime-local" name="datahora"
                                        class="form-control border-gray rounded-3 @error('datahora') is-invalid @enderror"
                                        value="{{ old('datahora', date('Y-m-d\TH:i')) }}">
                                    @error('datahora')
                                        <div class="invalid-feedback fw-bold">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Descrição --}}
                                <div class="col-12">
                                    <label class="small fw-bold text-muted text-uppercase mb-2 d-block">Descrição / Título
                                        do Documento</label>
                                    <input type="text" name="descricao"
                                        class="form-control border-gray rounded-3 @error('descricao') is-invalid @enderror"
                                        value="{{ old('descricao') }}"
                                        placeholder="Ex: RELATÓRIO DE GESTÃO FISCAL - RGF 1º QUADRIMESTRE">
                                    @error('descricao')
                                        <div class="invalid-feedback fw-bold">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Seção Dinâmica 1: Categoria Texto (Apenas para Prestação de Contas) --}}
                                <div class="col-12 d-none" id="secao_categoria_texto">
                                    <label class="small fw-bold text-muted text-uppercase mb-2 d-block">Nome da
                                        Categoria</label>
                                    <input type="text" name="categoria_texto"
                                        class="form-control border-gray rounded-3 @error('categoria_texto') is-invalid @enderror"
                                        value="{{ old('categoria_texto') }}"
                                        placeholder="Ex: BALANÇO ANUAL, CONTAS DO GOVERNANTE">
                                    @error('categoria_texto')
                                        <div class="invalid-feedback fw-bold">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Seção Dinâmica 2: Tags Globais (Apenas para Publicações Gerais) --}}
                                <div class="col-12" id="secao_tags_gerais">
                                    <label class="small fw-bold text-muted text-uppercase mb-2 d-block">Selecione as
                                        Tags/Categorias Vinculadas</label>
                                    <div class="card border border-gray rounded-3 p-3 bg-light">
                                        <div class="row g-2">
                                            @foreach ($tags as $tag)
                                                <div class="col-md-4 col-sm-6">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="tags[]"
                                                            value="{{ $tag->id }}" id="tag_{{ $tag->id }}"
                                                            {{ is_array(old('tags')) && in_array($tag->id, old('tags')) ? 'checked' : '' }}>
                                                        <label class="form-check-label text-dark small fw-semibold"
                                                            for="tag_{{ $tag->id }}">
                                                            {{ strtoupper($tag->nome) }}
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                {{-- Upload do Arquivo --}}
                                <div class="col-12">
                                    <label class="small fw-bold text-muted text-uppercase mb-2 d-block">Arquivo do
                                        Documento</label>
                                    <input type="file" name="arquivo"
                                        class="form-control border-gray rounded-3 @error('arquivo') is-invalid @enderror">
                                    <small class="text-muted mt-1 d-block">Formatos aceitos: PDF, Word, Excel ou ZIP. Limite
                                        de tamanho: 20MB.</small>
                                    @error('arquivo')
                                        <div class="invalid-feedback fw-bold">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Botões de Ação --}}
                                <div class="col-12 d-flex gap-2 pt-3 border-top mt-4">
                                    <button type="submit" class="btn btn-primary rounded-3 px-4 fw-bold shadow-sm">
                                        <i class="fas fa-save me-2"></i>Salvar Publicação
                                    </button>
                                    <a href="{{ route('publicacoes.index') }}"
                                        class="btn btn-light border rounded-3 px-4 text-secondary">
                                        Cancelar
                                    </a>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Script de Comportamento Dinâmico --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectTipo = document.getElementById('tipo_publicacao');
            const secaoCategoria = document.getElementById('secao_categoria_texto');
            const secaoTags = document.getElementById('secao_tags_gerais');

            function alternarCampos() {
                if (selectTipo.value === 'prestacao') {
                    secaoCategoria.classList.remove('d-none');
                    secaoTags.classList.add('d-none');
                } else {
                    secaoCategoria.classList.add('d-none');
                    secaoTags.classList.remove('d-none');
                }
            }

            selectTipo.addEventListener('change', alternarCampos);
            alternarCampos(); // Executa no carregamento caso volte com erro de validação
        });
    </script>

    <style>
        .bg-light-gray {
            background-color: #f8f9fa;
        }

        .border-gray {
            border: 1px solid #dee2e6;
        }

        .bg-light {
            background-color: #f1f5f9 !important;
        }
    </style>
@endsection
