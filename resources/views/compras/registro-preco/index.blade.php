@extends('layouts.app')
@section('content')
    <x-breadcrumb :items="['Compras' => '#', 'Registro de Preço' => '']" />

    <div class="container-fluid">
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form action="{{ route('compras.registro-preco.index') }}" method="GET" class="row g-2">
                    <div class="col-md-10">
                        <input type="text" name="busca" class="form-control"
                            placeholder="Buscar por produto ou serviço..." value="{{ request('busca') }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100"><i class="fa fa-search"></i> Filtrar</button>
                    </div>
                </form>
            </div>
        </div>

        <x-tabela-transparencia titulo="Itens com Preço Registrado" cor="dark" :colunas="[
            ['label' => 'Descrição', 'icone' => '', 'align' => 'text-start'],
            ['label' => 'Validade', 'icone' => '', 'align' => 'text-center'],
            ['label' => 'Qtd Min/Max', 'icone' => '', 'align' => 'text-center'],
            ['label' => 'Tipo/Situação', 'icone' => '', 'align' => 'text-center'],
            ['label' => 'Ação', 'icone' => '', 'align' => 'text-center'],
        ]">
            @foreach ($itens as $it)
                <tr>
                    <td>
                        <span class="badge bg-light text-dark border">{{ $it->item_codigo }}</span>
                        {{-- Ajustado para item_descricao conforme sua query --}}
                        <strong class="ms-2">{{ $it->item_descricao ?? 'Descrição não encontrada' }}</strong>
                    </td>
                    <td class="text-center">
                        {{ $it->data_validade ? \Carbon\Carbon::parse($it->data_validade)->format('d/m/Y') : '--' }}
                    </td>
                    <td class="text-center small">
                        Min: {{ number_format($it->quantidade_minima, 0, ',', '.') }}<br>
                        Max: {{ number_format($it->quantidade_maxima, 0, ',', '.') }}
                    </td>
                    <td class="text-center">
                        <small class="d-block">
                            {{ $it->tipo_registro == 'S' ? 'Serviço' : 'Produto' }}
                        </small>
                        {{-- Ajustado para usar registro_ativo que retorna 1 ou 0 --}}
                        @if ($it->registro_ativo == 1)
                            <span class="badge bg-success small">ATIVO</span>
                        @else
                            <span class="badge bg-danger small">INATIVO</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <a href="{{ route('compras.registro-preco.show', $it->item_codigo) }}"
                            class="btn btn-sm btn-outline-primary">
                            <i class="fa fa-users"></i> Fornecedores
                        </a>
                    </td>
                </tr>
            @endforeach
        </x-tabela-transparencia>
        @include('layouts.partials.back')
    </div>
@endsection
