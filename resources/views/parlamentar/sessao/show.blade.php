@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <x-breadcrumb :items="['Sessões' => route('parlamentar.sessao.index'), 'Detalhes da Sessão' => '']" />

        {{-- Resumo da Sessão --}}
        <div class="card shadow-sm border-0 mb-4 border-start border-4 border-primary">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <h3 class="fw-bold mb-0">{{ $sessao->descricao }}</h3>
                        <p class="text-muted">Realizada em {{ date('d/m/Y \à\s H:i', strtotime($sessao->horario)) }}</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <span class="d-block small text-muted">Quórum Mínimo</span>
                        <span class="fs-4 fw-bold">{{ $sessao->minquorum }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            {{-- Coluna da Esquerda: Pauta e Projetos --}}
            <div class="col-md-8">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white fw-bold">Proposições da Pauta</div>
                    <div class="card-body p-0">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light small">
                                <tr>
                                    <th>Data</th>
                                    <th>Autor</th>
                                    <th>Descrição</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($proposicoes as $p)
                                    <tr>
                                        <td class="small">{{ date('d/m/Y', strtotime($p->dataproposicao)) }}</td>
                                        <td class="small fw-bold">{{ $p->nome_autor }}</td>
                                        <td class="small">
                                            {{ $p->descricao }}
                                            @if ($p->idprotocolo)
                                                <div class="mt-1">
                                                    <a href="{{ route('parlamentar.sessao.projeto', $p->idprotocolo) }}"
                                                        class="btn btn-xs btn-link p-0 small">
                                                        <i class="fa fa-external-link-alt me-1"></i>Ver Tramitação do
                                                        Projeto
                                                    </a>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Coluna da Direita: Presenças e Votações --}}
            <div class="col-md-4">
                {{-- Presenças --}}
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-dark text-white fw-bold small">Presença dos Parlamentares</div>
                    <div class="card-body p-0" style="max-height: 300px; overflow-y: auto;">
                        <ul class="list-group list-group-flush">
                            @foreach ($presencas as $pres)
                                <li class="list-group-item d-flex justify-content-between align-items-center small">
                                    {{ $pres->nome }}
                                    <span class="badge {{ $pres->situacao == 'Presente' ? 'bg-success' : 'bg-danger' }}">
                                        {{ $pres->situacao }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                {{-- Votações --}}
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white fw-bold small">Resultado das Votações</div>
                    <div class="card-body p-2">
                        @foreach ($votacoes as $v)
                            <div class="border-bottom pb-2 mb-2">
                                <p class="mb-1 small fw-bold">{{ Str::limit($v->descricao, 50) }}</p>
                                <div class="d-flex justify-content-between">
                                    <span class="badge bg-success small">Sim: {{ (int) $v->favoravel }}</span>
                                    <span class="badge bg-danger small">Não: {{ (int) $v->contrario }}</span>
                                    <span class="badge bg-secondary small">Abs: {{ (int) $v->abstencao }}</span>
                                </div>
                                <small class="text-uppercase fw-bold text-primary mt-1 d-block" style="font-size: 0.7rem;">
                                    Resultado: {{ $v->situacao }}
                                </small>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
