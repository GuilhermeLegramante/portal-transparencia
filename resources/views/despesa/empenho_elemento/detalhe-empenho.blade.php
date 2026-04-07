@extends('layouts.app')

@section('content')
    <div class="container">
        <x-breadcrumb :items="[
            'Despesa' => '#',
            'Empenho Orçamentário' => route('empenho.elemento.index'),
            'Exercício ' . $exercicio => route('empenho.elemento.lista', $exercicio),
            'Elemento ' . $elemento->estrutural => route('empenho.elemento.detalhes', [$exercicio, $elemento->id]),
            'Empenho ' . $empenho->numero => '',
        ]" />

        @include('layouts.partials.elemento-despesa')

        @include('layouts.partials.empenho')

        <a href="{{ route('empenho.elemento.empenho.detalhe', [
            'exercicio' => $exercicio,
            'elemento_id' => $elemento->id,
            'empenho_id' => $empenho->id,
        ]) }}"
            class="btn btn-secondary shadow-sm">
            <i class="fa fa-arrow-left"></i> Voltar
        </a>
    </div>
@endsection
