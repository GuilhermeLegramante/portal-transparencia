@extends('layouts.app')

@section('content')
    <div class="container">
        <x-breadcrumb :items="[
            'Despesa' => '#',
            'Empenho Orçamentário' => route('empenho.orgao.index'),
            'Exercício ' . $exercicio => route('empenho.orgao.lista', $exercicio),
            $orgao->descricao => route('empenho.orgao.detalhes', [$exercicio, $orgao->id]),
            'Empenho ' . $empenho->numero => '',
        ]" />

        @include('layouts.partials.orgao')

        @include('layouts.partials.empenho')

        <a href="{{ route('empenho.orgao.empenho.detalhe', [
            'exercicio' => $exercicio,
            'orgao_id' => $orgao->id,
            'empenho_id' => $empenho->id,
        ]) }}"
            class="btn btn-secondary shadow-sm">
            <i class="fa fa-arrow-left"></i> Voltar
        </a>
    </div>
@endsection
