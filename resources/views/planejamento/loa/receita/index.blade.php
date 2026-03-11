@extends('layouts.app')

@section('content')
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb bg-light p-2 rounded">
                <li class="breadcrumb-item"><a href="/" class="text-decoration-none text-muted">Planejamento</a></li>
                <li class="breadcrumb-item text-muted">LOA</li>
                <li class="breadcrumb-item text-muted">Receita</li>
                <li class="breadcrumb-item active text-danger fw-bold" aria-current="page">{{ $breadcrumbTitle }}</li>
            </ol>
        </nav>

        @include('layouts.partials.cards.loa')

        @include('layouts.partials.tables.resumo-receita')

    </div>
@endsection
