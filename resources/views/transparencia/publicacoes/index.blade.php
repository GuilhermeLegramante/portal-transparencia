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
            @forelse ($publicacoes as $pub)
                <tr>
                    <td class="text-center text-muted small">{{ $pub->codigo }}</td> {{-- 1 --}}
                    <td class="text-center">{{ date('d/m/Y', strtotime($pub->data)) }}</td> {{-- 2 --}}
                    <td class="text-start">
                        <span class="fw-bold text-dark">{{ $pub->descricao }}</span>
                    </td> {{-- 3 --}}
                    <td>
                        {{-- Conteúdo das categorias... --}}
                    </td> {{-- 4 --}}
                    <td class="text-center">
                        {{-- Botão de documento... --}}
                    </td> {{-- 5 --}}
                </tr>
            @empty
                <tr>
                    {{-- O colspan DEVE ser igual ao número de colunas (5) --}}
                    <td colspan="5" class="text-center py-4 text-muted">
                        Nenhuma publicação encontrada.
                    </td>
                </tr>
            @endforelse

            {{-- SE VOCÊ ADICIONOU UM TFOOT, ELE DEVE TER 5 TDS OU COLSPAN QUE SOME 5 --}}
            <tfoot class="table-light fw-bold">
                <tr>
                    <td colspan="4" class="text-end">Total de Registros:</td>
                    <td class="text-center">{{ count($publicacoes) }}</td>
                </tr>
            </tfoot>
        </x-tabela-transparencia>
    </div>
@endsection
