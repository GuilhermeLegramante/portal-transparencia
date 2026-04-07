@extends('layouts.app')
@section('content')
    <div class="container py-4">
        <x-breadcrumb :items="[
            'Despesa' => '#',
            'Execução Orçamentária' => route('execucao.localizador.index'),
            'Por Localizador' => '',
        ]" />

        @include('layouts.partials.tables.resumo-execucao', [
            'detailsRoute' => 'execucao.localizador.list',
        ])
    </div>
@endsection
