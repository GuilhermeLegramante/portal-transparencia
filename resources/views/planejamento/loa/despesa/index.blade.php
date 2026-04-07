@extends('layouts.app')

@section('content')
    <div class="container">
        <x-breadcrumb :items="[
            'Planejamento' => '/',
            'LOA' => '#',
            'Despesa' => '#',
            $breadcrumbTitle => '',
        ]" />

        @include('layouts.partials.cards.loa')

        @include('layouts.partials.tables.resumo-despesa')

    </div>
@endsection
