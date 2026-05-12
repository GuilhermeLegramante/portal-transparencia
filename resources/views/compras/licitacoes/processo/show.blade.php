@extends('layouts.app')

@section('content')
    <x-breadcrumb :items="[
        'Compras' => '#',
        'Licitações' => route('compras.licitacoes.processo.index'),
        'Processo ' . $licitacao->processo => '',
    ]" />

    <div class="container-fluid">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Dados Gerais da Licitação</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3"><strong>Processo/Edital:</strong><br>{{ $licitacao->processo }} /
                        {{ $licitacao->edital }}</div>
                    <div class="col-md-6"><strong>Modalidade:</strong><br>{{ $licitacao->modalidade_nome }}</div>
                    <div class="col-md-3 text-end">
                        <strong>Abertura:</strong><br>{{ \Carbon\Carbon::parse($licitacao->dataabertura)->format('d/m/Y') }}
                    </div>
                    <div class="col-12 mt-3 text-muted border-top pt-2 italic"><strong>Objeto:</strong>
                        {{ $licitacao->descricao }}</div>
                </div>
            </div>
        </div>

        <ul class="nav nav-tabs border-0" id="licitacaoTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active fw-bold" data-bs-toggle="tab" href="#tab-itens">Itens Licitados</a>
            </li>
            <li class="nav-item">
                <a class="nav-link fw-bold" data-bs-toggle="tab" href="#tab-vencedores">Vencedores</a>
            </li>
            <li class="nav-item">
                <a class="nav-link fw-bold" data-bs-toggle="tab" href="#tab-comissao">Comissão Julgadora</a>
            </li>
        </ul>

        <div class="tab-content bg-white shadow-sm p-3 border">
            <div class="tab-pane fade show active" id="tab-itens">
                <table class="table table-hover align-middle datatable">
                    <thead class="table-light">
                        <tr>
                            <th>Nº</th>
                            <th>Descrição / Complemento</th>
                            <th class="text-center">Qtd</th>
                            <th class="text-end">Vlr Unitário</th>
                            <th class="text-center">Situação</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($itens as $it)
                            <tr>
                                <td class="fw-bold">{{ $it->numero }}</td>
                                <td>{{ $it->descricao }} <br><small class="text-muted">{{ $it->complemento }}</small></td>
                                <td class="text-center">{{ number_format($it->quantidade, 2, ',', '.') }}</td>
                                <td class="text-end">R$ {{ number_format($it->valor_unitario, 2, ',', '.') }}</td>
                                <td class="text-center">
                                    @php
                                        $sit = [0 => 'ABERTO', 1 => 'ADJUDICADO', 2 => 'FINALIZADO', 3 => 'CANCELADO'];
                                    @endphp
                                    <span class="badge border text-dark">{{ $sit[$it->situacao] ?? 'N/D' }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="tab-pane fade" id="tab-vencedores">
                @forelse($vencedores as $nome => $itensVencidos)
                    <div class="card mb-3 border">
                        <div class="card-header bg-light fw-bold text-primary">
                            <i class="fa fa-user-check me-2"></i> {{ $nome }}
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-sm mb-0">
                                <thead>
                                    <tr class="small text-muted">
                                        <th class="ps-3">Item</th>
                                        <th>Descrição</th>
                                        <th class="text-center">Qtd</th>
                                        <th class="text-end pe-3">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($itensVencidos as $v)
                                        <tr>
                                            <td class="ps-3">{{ $v->numero }}</td>
                                            <td>{{ $v->descricao }}</td>
                                            <td class="text-center">{{ number_format($v->quantidade, 0) }}</td>
                                            <td class="text-end pe-3 fw-bold">R$
                                                {{ number_format($v->valor_total, 2, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-light">
                                    <tr class="text-nowrap">
                                        <td colspan="3" class="text-end fw-bold">Total do Fornecedor:</td>
                                        <td class="text-end pe-3 text-primary fw-bold">R$
                                            {{ number_format($itensVencidos->sum('valor_total'), 2, ',', '.') }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-muted py-4">Nenhum vencedor registrado até o momento.</p>
                @endforelse
            </div>

            <div class="tab-pane fade" id="tab-comissao">
                <table class="table table-striped border">
                    <thead>
                        <tr>
                            <th>Inscrição</th>
                            <th>Nome do Integrante</th>
                            <th>Cargo</th>
                            <th>Hierarquia</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($comissao as $com)
                            <tr>
                                <td>{{ $com->inscricao }}</td>
                                <td class="fw-bold">{{ $com->nome }}</td>
                                <td>{{ $com->cargo }}</td>
                                <td><span class="badge bg-secondary">{{ $com->hierarquia }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
