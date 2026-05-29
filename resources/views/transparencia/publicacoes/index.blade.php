@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <x-breadcrumb :items="$breadcrumb" />

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('publicacoes.index') }}" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="small fw-bold">Exercício de Análise</label>
                        <input type="number" name="exercicio" class="form-control" value="{{ $exercicio }}" min="2000"
                            max="{{ date('Y') + 1 }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fa fa-search me-2"></i>Filtrar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <x-tabela-transparencia titulo="Publicações Realizadas - Exercício {{ $exercicio }}" :colunas="[
            ['label' => 'Código', 'align' => 'text-center'], // 1
            ['label' => 'Data', 'align' => 'text-center'], // 2
            ['label' => 'Descrição', 'align' => 'text-start'], // 3
            ['label' => 'Categorias', 'align' => 'text-start'], // 4
            ['label' => 'Documento', 'align' => 'text-center'], // 5
        ]">
            @foreach ($publicacoes as $pub)
                <tr class="align-middle">
                    {{-- 1. Código --}}
                    <td class="text-center text-muted small fw-semibold">
                        #{{ $pub->codigo }}
                    </td>

                    {{-- 2. Data --}}
                    <td class="text-center text-secondary text-nowrap">
                        {{ date('d/m/Y', strtotime($pub->data)) }}
                    </td>

                    {{-- 3. Descrição --}}
                    <td class="text-start">
                        <span class="fw-bold text-dark d-block mb-1">{{ $pub->descricao }}</span>
                        <span class="text-muted small d-block d-md-none">Exercício: {{ $pub->exercicio }}</span>
                    </td>

                    {{-- 4. Categorias (Tags) --}}
                    <td>
                        @if ($pub->categoria)
                            <div class="d-flex flex-wrap gap-1">
                                @foreach (explode(';', $pub->categoria) as $tag)
                                    <span
                                        class="badge bg-light text-primary border border-primary-subtle px-2 py-1 rounded-pill small fw-semibold">
                                        {{ trim($tag) }}
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <span class="text-muted small italic">Geral</span>
                        @endif
                    </td>

                    {{-- 5. Botão do Documento --}}
                    <td class="text-center">
                        @if ($pub->caminho_arquivo)
                            {{-- 
                   Utiliza o helper asset() apontando para a pasta pública storage.
                   Se os seus arquivos estiverem organizados por subpastas dentro da storage 
                   (ex: storage/public/cliente_2/arquivo.pdf), o banco já deve trazer o path relativo completo.
                --}}
                            <a href="{{ asset('storage/' . $pub->caminho_arquivo) }}" target="_blank"
                                class="btn btn-white btn-sm shadow-sm border rounded-3 px-3 fw-bold text-nowrap text-secondary btn-documento"
                                title="Visualizar Documento">
                                <i class="fas fa-file-pdf text-danger me-2"></i> Visualizar
                            </a>
                        @else
                            <span class="text-muted small">—</span>
                        @endif
                    </td>
                </tr>
            @endforeach

            <tfoot class="table-light fw-bold">
                <tr class="text-nowrap">
                    <td colspan="4" class="text-end">Total de Registros:</td>
                    <td class="text-center">{{ count($publicacoes) }}</td>
                </tr>
            </tfoot>
        </x-tabela-transparencia>
    </div>
@endsection
