@extends('layouts.app')

@section('content')
    <div class="container">
        <x-breadcrumb :items="[
            'Despesa' => '/',
            $breadcrumbTitle => '',
        ]" />

        @include('layouts.partials.cards.diarias')

        @include('layouts.partials.tables.resumo-diarias')

    </div>
@endsection
