@extends('layouts.app')
@section('content')
    <div class="container py-4">
        <x-breadcrumb :items="[
            'Receita' => '#',
            'Execução' => route('receita.execucao.elemento.index'),
            'Por Elemento' => '',
        ]" />

        @include('layouts.partials.tables.resumo-receita-menu-receita', [
            'detailsRoute' => 'receita.execucao.elemento.list',
        ])
    </div>
@endsection
