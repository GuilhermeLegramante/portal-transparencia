@extends('layouts.app')
@section('content')
    <x-breadcrumb :items="['Pessoal' => '#', 'Quadro Funcional' => '#', 'Relação Nominal' => '']" />

    <div class="container-fluid">
        @include('pessoal.quadro._resumo')

        <x-tabela-transparencia titulo="Relação Nominal de Servidores" cor="dark" :colunas="[
            ['label' => 'Matrícula', 'align' => 'text-center'],
            ['label' => 'Nome do Servidor', 'align' => 'text-start'],
            ['label' => 'Admissão', 'align' => 'text-center'],
            ['label' => 'Função', 'align' => 'text-start'],
            ['label' => 'Lotação', 'align' => 'text-start'],
        ]">
            @foreach ($dados as $d)
                <tr>
                    <td class="text-center">{{ $d->matricula }}</td>
                    <td class="fw-bold">{{ $d->nome }}</td>
                    <td class="text-center">{{ date('d/m/Y', strtotime($d->data_admissao)) }}</td>
                    <td><small>{{ $d->funcao }}</small></td>
                    <td><small>{{ $d->lotacao }}</small></td>
                </tr>
            @endforeach
        </x-tabela-transparencia>

        @include('layouts.partials.back')
    </div>
@endsection
