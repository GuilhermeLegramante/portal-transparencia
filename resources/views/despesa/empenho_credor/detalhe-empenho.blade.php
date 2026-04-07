@extends('layouts.app')

@section('content')
    <style>
        /* Força o comportamento correto da alternância sem quebrar o layout */
        .nav-tabs .nav-link.active {
            color: #0d6efd !important;
            /* Texto Azul quando ativa */
            background-color: #fff !important;
            /* Fundo Branco quando ativa */
        }

        .nav-tabs .nav-link:not(.active) {
            color: #ffffff !important;
            /* Texto Branco quando inativa */
            background-color: transparent !important;
            /* Fundo transparente quando inativa */
        }
    </style>
    <div class="container">
        <x-breadcrumb :items="[
            'Despesa' => '#',
            'Empenho Orçamentário' => route('empenho.credor.index'),
            'Credores de ' . $exercicio => route('empenho.credor.lista', $exercicio),
            'Credor: ' . $credor->nome => '',
            'Empenho: ' . $empenho->numero => '',
        ]" />

        @include('layouts.partials.credor')

        @include('layouts.partials.empenho')

        <a href="{{ route('empenho.credor.empenho.detalhe', [
            'exercicio' => $exercicio,
            'credor_id' => $credor->inscricao, // Ajustado de id para inscricao
            'empenho_id' => $empenho->id,
        ]) }}"
            class="btn btn-secondary shadow-sm">
            <i class="fa fa-arrow-left"></i> Voltar
        </a>
    </div>
@endsection
