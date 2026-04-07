@extends('layouts.app')
@section('content')
    <div class="container py-4">
        <x-breadcrumb :items="[
            'Despesa' => '#',
            'Execução Orçamentária' => route('execucao.recurso.index'),
            'Por Recurso' => '',
        ]" />

        @include('layouts.partials.tables.resumo-execucao', ['detailsRoute' => 'execucao.recurso.list'])
    </div>
@endsection
