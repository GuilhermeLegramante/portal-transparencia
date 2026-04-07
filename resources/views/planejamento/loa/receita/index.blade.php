@extends('layouts.app')

@section('content')
    <div class="container">
        <x-breadcrumb :items="[
            'Planejamento' => '/',
            'LOA' => '#',
            'Receita' => '#',
            $breadcrumbTitle => '',
        ]" />

        @include('layouts.partials.cards.loa')

        @include('layouts.partials.tables.resumo-receita')

    </div>
@endsection
