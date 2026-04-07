@extends('layouts.app')
@section('content')
    <div class="container py-4">
        <x-breadcrumb :items="[
            'Despesa' => '#',
            'Execução Orçamentária' => route('execucao.orgao.index'),
            'Por Órgão' => '',
        ]" />

        @include('layouts.partials.tables.resumo-execucao', ['detailsRoute' => 'execucao.orgao.list'])
    </div>
@endsection
