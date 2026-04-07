@extends('layouts.app')
@section('content')
    <div class="container py-4">
        <x-breadcrumb :items="[
            'Receita' => '#',
            'Arrecadação' => route('receita.arrecadacao.elemento.index'),
            'Por Elemento' => '',
        ]" />

        @include('layouts.partials.tables.resumo-receita-menu-receita', [
            'detailsRoute' => 'receita.arrecadacao.elemento.list',
        ])
    </div>
@endsection
