<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-start border-success border-4 shadow-sm">
            <div class="card-body">
                <div class="text-muted small uppercase font-weight-bold">Em Atividade</div>
                <div class="h4 mb-0">{{ $resumo->atividade }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-start border-info border-4 shadow-sm">
            <div class="card-body">
                <div class="text-muted small uppercase font-weight-bold">Afastados</div>
                <div class="h4 mb-0">{{ $resumo->afastado_calculado + $resumo->afastado_nao_calculado }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-start border-danger border-4 shadow-sm">
            <div class="card-body">
                <div class="text-muted small uppercase font-weight-bold">Exonerados</div>
                <div class="h4 mb-0">{{ $resumo->exonerado }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-start border-dark border-4 shadow-sm">
            <div class="card-body">
                <div class="text-muted small uppercase font-weight-bold">Total Quadro</div>
                <div class="h4 mb-0">{{ $resumo->total }}</div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4 shadow-sm">
    <div class="card-body">
        <form method="GET" action="" class="row align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-bold">Filtrar por Situação:</label>
                <select name="situacao" class="form-select" onchange="this.form.submit()">
                    @foreach ($situacoes as $s)
                        <option value="{{ $s->id }}" {{ $sitId == $s->id ? 'selected' : '' }}>{{ $s->nome }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>
</div>
