<div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; overflow: hidden;">
    <div class="card-header bg-white border-bottom py-3">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center">
                <div class="icon-box-small bg-soft-blue text-primary me-3">
                    <i class="fa fa-user-circle"></i>
                </div>
                <h5 class="mb-0 text-dark fw-bold" style="font-family: 'Inter', sans-serif;">Dados do Credor</h5>
            </div>

            <ul class="nav nav-pills nav-pills-custom" id="credorTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="ident-tab" data-bs-toggle="tab" data-bs-target="#tab-ident"
                        type="button" role="tab">Identificação</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="local-tab" data-bs-toggle="tab" data-bs-target="#tab-local"
                        type="button" role="tab">Localização</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="contato-tab" data-bs-toggle="tab" data-bs-target="#tab-contato"
                        type="button" role="tab">Contato</button>
                </li>
            </ul>
        </div>
    </div>

    <div class="card-body p-0 tab-content">
        {{-- ABA IDENTIFICAÇÃO --}}
        <div class="tab-pane fade show active" id="tab-ident" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <tbody class="text-secondary">
                        <tr>
                            <td class="ps-4 text-muted fw-semibold" width="220">Inscrição</td>
                            <td class="text-dark">{{ $credor->inscricao }}</td>
                        </tr>
                        <tr>
                            <td class="ps-4 text-muted fw-semibold">Nome / Razão Social</td>
                            <td class="text-primary fw-bold">{{ $credor->nome }}</td>
                        </tr>
                        <tr>
                            <td class="ps-4 text-muted fw-semibold">CPF/CNPJ</td>
                            <td class="text-dark">
                                @if ($credor->tipo_pessoa == 'F')
                                    {{ $credor->cpf ?? '***.***.***-**' }}
                                @else
                                    {{ $credor->cnpj ?? '**.***.***/****-**' }}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="ps-4 text-muted fw-semibold border-0">Tipo Pessoa</td>
                            <td class="border-0">
                                <span class="badge bg-soft-blue text-primary px-3 py-2 rounded-pill fw-bold">
                                    {{ $credor->tipo_pessoa == 'F' ? 'PESSOA FÍSICA' : 'PESSOA JURÍDICA' }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ABA LOCALIZAÇÃO --}}
        <div class="tab-pane fade" id="tab-local" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <tbody class="text-secondary">
                        <tr>
                            <td class="ps-4 text-muted fw-semibold" width="220">Endereço</td>
                            <td class="text-dark">
                                {{ $credor->nome_logradouro ?? 'Não informado' }}{{ $credor->numero_imovel ? ', ' . $credor->numero_imovel : ', S/N' }}
                            </td>
                        </tr>
                        <tr>
                            <td class="ps-4 text-muted fw-semibold">Bairro</td>
                            <td class="text-dark">{{ $credor->nome_bairro ?? 'Não informado' }}</td>
                        </tr>
                        <tr>
                            <td class="ps-4 text-muted fw-semibold">Cidade/UF</td>
                            <td class="text-dark">
                                @if ($credor->nome_municipio)
                                    {{ $credor->nome_municipio }} / {{ $credor->uf ?? 'XX' }}
                                @else
                                    <span class="text-muted italic small">Não informado</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="ps-4 text-muted fw-semibold border-0">CEP</td>
                            <td class="text-dark border-0">{{ $credor->cep ?? 'Não informado' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ABA CONTATO --}}
        <div class="tab-pane fade" id="tab-contato" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <tbody class="text-secondary">
                        <tr>
                            <td class="ps-4 text-muted fw-semibold" width="220">Telefone</td>
                            <td class="text-dark">{{ $credor->telefone ?? 'Não informado' }}</td>
                        </tr>
                        <tr>
                            <td class="ps-4 text-muted fw-semibold">Celular</td>
                            <td class="text-dark">{{ $credor->celular ?? 'Não informado' }}</td>
                        </tr>
                        <tr>
                            <td class="ps-4 text-muted fw-semibold border-0">E-mail</td>
                            <td class="text-dark border-0">
                                @if ($credor->email)
                                    <a href="mailto:{{ $credor->email }}"
                                        class="text-decoration-none">{{ $credor->email }}</a>
                                @else
                                    <span class="text-muted italic small">Não informado</span>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
