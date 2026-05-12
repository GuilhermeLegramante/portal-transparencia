<div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; overflow: hidden;">
    <div class="card-header bg-white border-bottom py-3">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center">
                <div class="icon-box-small bg-soft-blue text-primary me-3">
                    <i class="fa fa-info-circle"></i>
                </div>
                <h5 class="mb-0 text-dark fw-bold" style="font-family: 'Inter', sans-serif;">Dados do Empenho</h5>
            </div>

            <ul class="nav nav-pills nav-pills-custom" id="empenhoTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="ident-tab" data-bs-toggle="tab" data-bs-target="#ident"
                        type="button" role="tab">Identificação</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="dotac-tab" data-bs-toggle="tab" data-bs-target="#dotac" type="button"
                        role="tab">Dotação</button>
                </li>
            </ul>
        </div>
    </div>

    <div class="card-body p-0 tab-content">
        {{-- TAB IDENTIFICAÇÃO --}}
        <div class="tab-pane fade show active" id="ident" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <tbody class="text-secondary">
                        <tr>
                            <td class="ps-4 text-muted fw-semibold" width="220">Número / Exercício</td>
                            <td class="text-dark fw-bold">{{ $empenho->numero }} / {{ $exercicio }}</td>
                        </tr>
                        <tr>
                            <td class="ps-4 text-muted fw-semibold">Data de Emissão</td>
                            <td class="text-dark">{{ date('d/m/Y', strtotime($empenho->dataemissao)) }}</td>
                        </tr>
                        <tr>
                            <td class="ps-4 text-muted fw-semibold">Credor / CPF-CNPJ</td>
                            <td>
                                <span class="fw-bold text-primary">{{ $empenho->nome_municipe }}</span>
                                <span class="ms-1 text-muted small">({{ $empenho->documento }})</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="ps-4 text-muted fw-semibold">Modalidade / Espécie</td>
                            <td class="text-dark">{{ $empenho->modalidade }} / {{ $empenho->especie }}</td>
                        </tr>
                        <tr>
                            <td class="ps-4 text-muted fw-semibold">Tipo</td>
                            <td>
                                @if ($empenho->tipo == 'O')
                                    <span class="badge bg-soft-blue text-primary px-3 py-2 rounded-pill fw-bold"
                                        style="font-size: 0.7rem;">
                                        ORÇAMENTÁRIO
                                    </span>
                                @else
                                    <span class="badge bg-soft-success text-success px-3 py-2 rounded-pill fw-bold"
                                        style="font-size: 0.7rem;">
                                        RESTOS A PAGAR
                                    </span>
                                @endif
                            </td>
                        </tr>
                        {{-- <tr>
                            <td class="ps-4 text-muted fw-semibold border-0">Objeto / Finalidade</td>
                            <td class="text-dark border-0 py-3">{{ $empenho->objeto }}</td>
                        </tr> --}}
                    </tbody>
                </table>
            </div>
        </div>

        {{-- TAB DOTAÇÃO --}}
        <div class="tab-pane fade" id="dotac" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <tbody class="text-secondary">
                        <tr>
                            <td class="ps-4 text-muted fw-semibold" width="220">Órgão / Unidade</td>
                            <td class="text-dark">{{ $empenho->orgao }} - {{ $empenho->unidade }}</td>
                        </tr>
                        <tr>
                            <td class="ps-4 text-muted fw-semibold">Funcional Programática</td>
                            <td class="text-dark font-monospace small">{{ $empenho->funcao }} .
                                {{ $empenho->sub_funcao }} . {{ $empenho->programa }}</td>
                        </tr>
                        <tr>
                            <td class="ps-4 text-muted fw-semibold">Elemento de Despesa</td>
                            <td class="text-primary fw-bold">{{ $empenho->elemento }}</td>
                        </tr>
                        <tr>
                            <td class="ps-4 text-muted fw-semibold border-0">Fonte de Recurso</td>
                            <td class="text-dark border-0">{{ $empenho->vinculo }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
{{-- 2. ITENS DO EMPENHO --}}
@php
    $columns = [
        ['label' => 'Número', 'icone' => '', 'align' => 'text-center'],
        ['label' => 'Descrição', 'icone' => '', 'align' => 'text-start'],
        ['label' => 'Quantidade', 'icone' => '', 'align' => 'text-center'],
        ['label' => 'Valor unitário', 'icone' => '', 'align' => 'text-end'],
        ['label' => 'Total', 'icone' => '', 'align' => 'text-end'],
    ];
@endphp

<x-tabela-transparencia titulo="Itens do empenho" cor="primary" :colunas="$columns">
    @foreach ($itens as $it)
        <tr>
            <td class="text-center">{{ $it->numero }}</td>
            <td class="text-start small">{{ $it->descricao }}</td>
            <td class="text-center">R$ {{ number_format($it->quantidade, 2, ',', '.') }}</td>
            <td class="text-end">R$ {{ number_format($it->valor_unitario, 4, ',', '.') }}</td>
            <td class="text-end fw-bold">R$ {{ number_format($it->valor_total, 2, ',', '.') }}</td>
        </tr>
    @endforeach
    {{-- No seu empenho.blade.php --}}
    <tfoot class="table-light fw-bold">
        <tr>
            <td></td>
            <td></td>
            <td></td> {{-- Células vazias em vez de colspan --}}
            <td class="text-end">TOTAL DOS ITENS:</td>
            <td class="text-end text-primary">R$ {{ number_format($itens->sum('valor_total'), 2, ',', '.') }}</td>
        </tr>
    </tfoot>
</x-tabela-transparencia>
