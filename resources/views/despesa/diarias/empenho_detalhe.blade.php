@extends('layouts.app')

@section('content')
    <style>

    </style>
    <div class="container">
        <x-breadcrumb :items="[
            'Despesa' => '/',
            'Diárias' => route('despesa.diarias.resumo'),
            'Exercício ' . $exercicio => route('despesa.diarias.detalhe', ['exercicio' => $exercicio]),
            $empenho->nome_municipe => route('despesa.diarias.credor', [
                'exercicio' => $exercicio,
                'cad' => $credor_id,
            ]),
            'Empenho nº ' . $empenho->numero => '',
        ]" />

        @include('layouts.partials.credor')

        @include('layouts.partials.empenho')

        <div class="mt-4">
            <a href="{{ route('despesa.diarias.credor', ['exercicio' => $exercicio, 'cad' => $credor_id]) }}"
                class="btn btn-secondary shadow-sm">
                <i class="fa fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
@endsection
