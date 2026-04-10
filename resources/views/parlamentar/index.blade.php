@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <x-breadcrumb :items="['Parlamentar' => '#', 'Legislaturas' => '']" />

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="small fw-bold">Selecione a Legislatura</label>
                        <select name="legislatura" class="form-select" onchange="this.form.submit()">
                            @foreach ($legislaturas as $l)
                                <option value="{{ $l->id }}" {{ $legSelecionada == $l->id ? 'selected' : '' }}>
                                    {{ $l->descricao }} ({{ date('d/m/Y', strtotime($l->datainicio)) }} -
                                    {{ date('d/m/Y', strtotime($l->datatermino)) }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
        </div>

        {{-- Navegação das Abas --}}
        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="pills-parlamentares-tab" data-bs-toggle="pill"
                    data-bs-target="#pills-parlamentares" type="button" role="tab" aria-controls="pills-parlamentares"
                    aria-selected="true">
                    Parlamentares
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pills-mesa-tab" data-bs-toggle="pill" data-bs-target="#pills-mesa"
                    type="button" role="tab" aria-controls="pills-mesa" aria-selected="false">
                    Mesa Diretora
                </button>
            </li>
        </ul>

        {{-- Conteúdo das Abas --}}
        <div class="tab-content" id="pills-tabContent">

            {{-- Aba Parlamentares --}}
            <div class="tab-pane fade show active" id="pills-parlamentares" role="tabpanel"
                aria-labelledby="pills-parlamentares-tab">
                <x-tabela-transparencia titulo="Lista de Parlamentares" :colunas="[
                    ['label' => 'Nome', 'align' => 'text-start'],
                    ['label' => 'Partido', 'align' => 'text-center'],
                    ['label' => 'Votos', 'align' => 'text-center'],
                    ['label' => 'Tipo', 'align' => 'text-center'],
                    ['label' => 'Situação', 'align' => 'text-center'],
                ]">
                    @foreach ($parlamentares as $p)
                        <tr>
                            <td class="fw-bold">{{ $p->nome }}</td>
                            <td class="text-center"><span class="badge bg-secondary">{{ $p->partido }}</span></td>
                            <td class="text-center">{{ number_format($p->voto, 0, '', '.') }}</td>
                            <td class="text-center small">{{ $p->tipo }}</td>
                            <td class="text-center">
                                <span class="badge {{ $p->situacao == 'Ativo' ? 'bg-success' : 'bg-warning text-dark' }}">
                                    {{ $p->situacao }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </x-tabela-transparencia>
            </div>

            {{-- Aba Mesa Diretora --}}
            <div class="tab-pane fade" id="pills-mesa" role="tabpanel" aria-labelledby="pills-mesa-tab">
                <x-tabela-transparencia titulo="Mesa Diretora" :colunas="[
                    ['label' => 'Mesa / Cargo', 'align' => 'text-start'],
                    ['label' => 'Parlamentar', 'align' => 'text-start'],
                    ['label' => 'Partido', 'align' => 'text-center'],
                ]">
                    @foreach ($mesas as $tituloMesa => $integrantes)
                        {{-- Linha de destaque para o título da Mesa --}}
                        <tr class="bg-light">
                            <td colspan="3" class="fw-bold text-primary">{{ $tituloMesa }}</td>
                        </tr>

                        @foreach ($integrantes as $i)
                            <tr>
                                <td class="ps-4 fw-bold">{{ $i->cargo }}</td>
                                <td>{{ $i->nome }}</td>
                                <td class="text-center">
                                    <span class="badge bg-secondary">{{ $i->partido }}</span>
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                </x-tabela-transparencia>
            </div>
        </div>
    </div>
    </div>
@endsection
