@extends('layouts.app')
@section('content')
    <div class="container py-4">
        <x-breadcrumb :items="[
            'Receita' => '#',
            'Arrecadação' => route('receita.arrecadacao.recurso.index'),
            'Por Recurso' => '',
        ]" />

        @include('layouts.partials.tables.resumo-receita-menu-receita', [
            'detailsRoute' => 'receita.arrecadacao.recurso.list',
        ])
    </div>
@endsection
