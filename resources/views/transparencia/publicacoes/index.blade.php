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
            ['label' => 'Código', 'align' => 'text-center'],
            ['label' => 'Data', 'align' => 'text-center'],
            ['label' => 'Descrição', 'align' => 'text-start'],
            ['label' => 'Categorias', 'align' => 'text-start'],
            ['label' => 'Documento', 'align' => 'text-center'],
        ]">
            @forelse ($publicacoes as $pub)
                <tr>
                    <td class="text-center text-muted small">{{ $pub->codigo }}</td>
                    <td class="text-center">{{ date('d/m/Y', strtotime($pub->data)) }}</td>
                    <td class="text-start">
                        <span class="fw-bold text-dark">{{ $pub->descricao }}</span>
                    </td>
                    <td>
                        @php $tags = explode(';', $pub->categoria); @endphp
                        @foreach ($tags as $tag)
                            @if (!empty($tag))
                                <span class="badge bg-light text-dark border shadow-sm small mb-1">
                                    {{ trim($tag) }}
                                </span>
                            @endif
                        @endforeach
                    </td>
                    <td class="text-center">
                        @if ($pub->caminho_arquivo)
                            <a href="{{ asset('storage/' . $pub->caminho_arquivo) }}" target="_blank"
                                class="btn btn-sm btn-outline-danger">
                                <i class="fa fa-file-pdf me-1"></i> Visualizar
                            </a>
                        @else
                            <span class="text-muted small italic">Não disponível</span>
                        @endif
                    </td>
                </tr>
            @empty
                {{-- Importante: O DataTables às vezes reclama se houver um TR dentro do tbody 
             que não segue a contagem exata de colunas durante a inicialização --}}
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">
                        <i class="fa fa-info-circle me-2"></i>Nenhuma publicação encontrada.
                    </td>
                </tr>
            @endforelse

            <tfoot class="table-light fw-bold">
                <tr>
                    {{-- Se você quer uma linha de totalizadores, use 5 TDs --}}
                    <td class="text-end" colspan="2">TOTAL DE REGISTROS:</td>
                    <td class="text-start">{{ count($publicacoes) }}</td>
                    <td></td> {{-- Coluna Categorias --}}
                    <td></td> {{-- Coluna Documento --}}
                </tr>
            </tfoot>
        </x-tabela-transparencia>
    </div>
@endsection
