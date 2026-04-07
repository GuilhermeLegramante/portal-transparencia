@extends('layouts.app')
@section('content')
    <div class="container py-4">
        <x-breadcrumb :items="[
            'Despesa' => '#',
            'Execução Orçamentária' => route('execucao.elemento.index'),
            'Por Elemento' => '',
        ]" />

        @include('layouts.partials.tables.resumo-execucao', ['detailsRoute' => 'execucao.elemento.list'])
    </div>
@endsection
