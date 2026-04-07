@extends('layouts.app')

@section('content')
    <div class="container">
        <x-breadcrumb :items="[
            'Despesa' => '#',
            'Empenho Orçamentário' => route('empenho.recurso.index'),
            'Exercício ' . $exercicio => route('empenho.recurso.lista', $exercicio),
            $recurso->descricao => route('empenho.recurso.detalhes', [$exercicio, $recurso->id]),
            'Empenho ' . $empenho->numero => '',
        ]" />

        @include('layouts.partials.recurso')

        @include('layouts.partials.empenho')

        <a href="{{ route('empenho.recurso.empenho.detalhe', [
            'exercicio' => $exercicio,
            'recurso_id' => $recurso->id,
            'empenho_id' => $empenho->id,
        ]) }}"
            class="btn btn-secondary shadow-sm">
            <i class="fa fa-arrow-left"></i> Voltar
        </a>
    </div>
@endsection
