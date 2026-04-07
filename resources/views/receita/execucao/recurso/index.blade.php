@extends('layouts.app')
@section('content')
    <div class="container py-4">
        <x-breadcrumb :items="[
            'Receita' => '#',
            'Execução' => route('receita.execucao.recurso.index'),
            'Por Recurso' => '',
        ]" />

        @include('layouts.partials.tables.resumo-receita-menu-receita', [
            'detailsRoute' => 'receita.execucao.recurso.list',
        ])
    </div>
@endsection
