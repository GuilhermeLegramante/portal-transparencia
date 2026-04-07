@extends('layouts.app')
@section('content')
    <x-breadcrumb :items="[
        'Pessoal' => '#',
        'Quadro Funcional' => '#',
        'Detalhes por ' . $config['titulo'] => '',
    ]" />

    <div class="container-fluid">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body bg-light">
                <h5 class="mb-0">
                    <i class="fa fa-users text-primary me-2"></i>
                    {{ $config['titulo'] }}: <span class="text-primary">{{ $categoriaNome }}</span>
                </h5>
            </div>
        </div>

        <x-tabela-transparencia :titulo="'Lista de Servidores'" cor="dark" :colunas="[
            ['label' => 'Matrícula', 'align' => 'text-center'],
            ['label' => 'Nome do Servidor', 'align' => 'text-start'],
            ['label' => 'Data de Admissão', 'align' => 'text-center'],
            ['label' => 'Ação', 'align' => 'text-center'],
        ]">
            @foreach ($servidores as $s)
                <tr>
                    <td class="text-center">{{ $s->matricula }}</td>
                    <td class="fw-bold">{{ $s->nome }}</td>
                    <td class="text-center">{{ date('d/m/Y', strtotime($s->data_admissao)) }}</td>
                    <td class="text-center">
                        {{-- Espaço para link de ficha financeira ou funcional futura --}}
                        <span class="badge bg-secondary">Ver Ficha</span>
                    </td>
                </tr>
            @endforeach
        </x-tabela-transparencia>

        @include('layouts.partials.back')
    </div>
@endsection
