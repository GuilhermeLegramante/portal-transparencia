<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class EmpenhoRepository
{
    /**
     * Busca o detalhamento completo de um empenho específico.
     */
    public function findById(int $empId, int $exercicio, int $idCliente)
    {
        return DB::table('ctbempenho as empenho')
            ->join('cadmunicipe as municipe', function ($j) {
                $j->on('municipe.id', '=', 'empenho.idcredor')
                    ->on('municipe.idcliente', '=', 'empenho.idcliente');
            })
            ->join('ctbcontadespesa as contaDespesa', function ($j) {
                $j->on('contaDespesa.id', '=', 'empenho.iddespesa')
                    ->on('contaDespesa.idcliente', '=', 'empenho.idcliente');
            })
            ->join('ctbunidadeorcamentaria as unidade', function ($j) {
                $j->on('unidade.id', '=', 'contaDespesa.idunidadeorcamentaria')
                    ->on('unidade.idcliente', '=', 'contaDespesa.idcliente');
            })
            ->select([
                'empenho.id',
                'empenho.numero',
                'empenho.dataemissao',
                'empenho.tipo',
                'empenho.idcredor', // Útil caso precise linkar para o credor
                'municipe.nome as nome_municipe',
                'contaDespesa.codigo as codigo_despesa',
                'unidade.nome as unidade'
            ])
            ->selectRaw("IFNULL(municipe.cpf, municipe.cnpj) AS documento")

            // Subqueries consolidadas
            ->selectRaw("(SELECT nome FROM ctbmodalidadelicitacao WHERE id = empenho.idlicitacao AND idcliente = empenho.idcliente) as modalidade")
            ->selectRaw("(SELECT nome FROM ctbespecieempenho WHERE id = empenho.idespecie AND idcliente = empenho.idcliente) as especie")
            ->selectRaw("(SELECT nome FROM ctbobjetivodespesa WHERE id = empenho.idobjetivo AND idcliente = empenho.idcliente) as objeto")
            ->selectRaw("(SELECT nome FROM ctborgao WHERE id = unidade.idorgao AND idcliente = unidade.idcliente) as orgao")
            ->selectRaw("(SELECT nome FROM ctbfuncao WHERE id = contaDespesa.idfuncao AND idcliente = contaDespesa.idcliente) as funcao")
            ->selectRaw("(SELECT nome FROM ctbsubfuncao WHERE id = contaDespesa.idsubfuncao AND idcliente = contaDespesa.idcliente) as sub_funcao")
            ->selectRaw("(SELECT nome FROM ctbprograma WHERE id = contaDespesa.idprograma AND idcliente = contaDespesa.idcliente) as programa")
            ->selectRaw("(SELECT nome FROM ctbsubprograma WHERE id = contaDespesa.idsubprograma AND idcliente = contaDespesa.idcliente) as sub_programa")
            ->selectRaw("(SELECT nome FROM ctbprojeto WHERE id = contaDespesa.idprojeto AND idcliente = contaDespesa.idcliente) as projeto")
            ->selectRaw("(SELECT CONCAT(estrutural, ' - ', nome) FROM ctbelemento WHERE id = contaDespesa.idelemento AND idcliente = contaDespesa.idcliente) as elemento")
            ->selectRaw("(SELECT nome FROM ctbunidadegestora WHERE id = contaDespesa.idgestora AND idcliente = contaDespesa.idcliente) as gestora")
            ->selectRaw("(SELECT nome FROM ctbrecursovinculado WHERE id = empenho.idrecurso AND idcliente = empenho.idcliente) as vinculo")

            ->where('empenho.idcliente', $idCliente)
            ->where('empenho.exercicio', $exercicio)
            ->where('empenho.id', $empId)
            ->first();
    }

    public function findItemsByEmpenhoId(int $empId, int $idCliente)
    {
        return DB::table('ctbempenhoitem')
            ->select([
                'numero',
                'descricao',
                'quantidade',
                // Define o alias para valorunitario
                'valorunitario as valor_unitario',
            ])
            ->selectRaw("(quantidade * valorunitario) AS valor_total") // Calcula o total conforme seu SQL
            ->where('idcliente', $idCliente)
            ->where('idempenho', $empId)
            ->get();
    }

    public function findAllByCredorId(int $credorId, int $exercicio, int $idCliente)
    {
        return DB::table('ctbempenhomovimento as movimento')
            ->join('ctbempenho as empenho', function ($join) {
                $join->on('empenho.id', '=', 'movimento.idempenho')
                    ->on('empenho.idcliente', '=', 'movimento.idcliente');
            })
            ->join('ctbcontadespesa as contaDespesa', function ($join) {
                $join->on('contaDespesa.id', '=', 'empenho.iddespesa')
                    ->on('contaDespesa.idcliente', '=', 'empenho.idcliente');
            })
            ->join('ctbelemento as elemento', function ($join) {
                $join->on('elemento.id', '=', 'contaDespesa.idelemento')
                    ->on('elemento.idcliente', '=', 'contaDespesa.idcliente');
            })
            ->select([
                'movimento.idcliente as cliente_id',
                'movimento.idempenho as empenho_id',
                'empenho.idcredor as credor_id',
                'empenho.exercicio',
                'empenho.dataemissao as data_emissao',
                'empenho.numero',
                'empenho.tipo',
            ])
            ->selectRaw("SUM(movimento.emissao) AS empenhado")
            ->selectRaw("SUM(movimento.anular) AS anulado")
            ->selectRaw("SUM(movimento.liquidacao) AS liquidado")
            ->selectRaw("SUM(movimento.pagamento) AS pago")
            ->selectRaw("SUM(movimento.emissao - movimento.anular) AS saldo_empenhado")
            ->selectRaw("SUM(movimento.emissao - movimento.anular - movimento.liquidacao) AS saldo_liquidar")
            ->selectRaw("SUM(movimento.liquidacao - movimento.pagamento) AS saldo_pagar")
            ->where('movimento.idcliente', $idCliente)
            ->where('empenho.exercicio', $exercicio)
            ->where('empenho.idcredor', $credorId)
            ->where(function ($query) {
                $query->where('elemento.estrutural', 'like', '3.3.9.0.14.%')
                    ->orWhere('elemento.estrutural', 'like', '3.3.9.2.14.%')
                    ->orWhere('elemento.estrutural', 'like', '3.3.9.5.14.%')
                    ->orWhere('elemento.estrutural', 'like', '3.3.9.6.14.%')
                    ->orWhere('elemento.estrutural', 'like', '4.4.9.0.14.%');
            })
            ->groupBy(
                'movimento.idcliente',
                'movimento.idempenho',
                'empenho.idcredor',
                'empenho.exercicio',
                'empenho.dataemissao',
                'empenho.numero',
                'empenho.tipo'
            )
            ->orderByDesc('empenho.dataemissao')
            ->get();
    }

    public function resumoAnualPorExercicio($idCliente)
    {
        return DB::table('ctbempenhomovimento as m')
            ->join('ctbempenho as e', function ($join) {
                $join->on('e.id', '=', 'm.idempenho')
                    ->on('e.idcliente', '=', 'm.idcliente');
            })
            ->where('m.idcliente', $idCliente)
            ->select(
                'e.exercicio',
                DB::raw('SUM(m.emissao - m.anular) as valor')
            )
            ->groupBy('e.exercicio')
            ->orderBy('e.exercicio', 'desc')
            ->get();
    }

    /**
     * Retorna o resumo financeiro agrupado por Elemento de Despesa
     */
    public function listaElementos(int $exercicio, int $idCliente)
    {
        return DB::table('ctbempenhomovimento as movimento')
            ->join('ctbempenho as empenho', function ($j) {
                $j->on('empenho.id', '=', 'movimento.idempenho')
                    ->on('empenho.idcliente', '=', 'movimento.idcliente');
            })
            ->join('ctbcontadespesa as contaDespesa', function ($j) {
                $j->on('contaDespesa.id', '=', 'empenho.iddespesa')
                    ->on('contaDespesa.idcliente', '=', 'empenho.idcliente');
            })
            ->join('ctbelemento as elemento', function ($j) {
                $j->on('elemento.id', '=', 'contaDespesa.idelemento')
                    ->on('elemento.idcliente', '=', 'contaDespesa.idcliente');
            })
            ->select([
                'movimento.idcliente as cliente_id',
                'contaDespesa.idelemento as elemento_id',
                'empenho.exercicio',
                'elemento.estrutural',
                'elemento.nome',
            ])
            // Cálculos de Totais
            ->selectRaw("SUM(movimento.emissao) AS total_empenhado")
            ->selectRaw("SUM(movimento.anular) AS total_anulado")
            ->selectRaw("SUM(movimento.liquidacao) AS total_liquidado")
            ->selectRaw("SUM(movimento.pagamento) AS total_pago")
            // Cálculos de Saldos
            ->selectRaw("SUM(movimento.emissao - movimento.anular) AS saldo_empenhado")
            ->selectRaw("SUM(movimento.emissao - movimento.anular - movimento.liquidacao) AS saldo_liquidar")
            ->selectRaw("SUM(movimento.liquidacao - movimento.pagamento) AS saldo_pagar")

            ->where('movimento.idcliente', $idCliente)
            ->where('empenho.exercicio', $exercicio)

            ->groupBy(
                'movimento.idcliente',
                'empenho.exercicio',
                'contaDespesa.idelemento',
                'elemento.estrutural',
                'elemento.nome'
            )
            ->orderBy('saldo_empenhado', 'DESC')
            ->get();
    }

    public function findEmpenhosByElemento(int $elementoId, int $exercicio, int $idCliente)
    {
        return DB::table('ctbempenhomovimento as movimento')
            ->join('ctbempenho as empenho', function ($j) {
                $j->on('empenho.id', '=', 'movimento.idempenho')
                    ->on('empenho.idcliente', '=', 'movimento.idcliente');
            })
            ->join('ctbcontadespesa as contaDespesa', function ($j) {
                $j->on('contaDespesa.id', '=', 'empenho.iddespesa')
                    ->on('contaDespesa.idcliente', '=', 'empenho.idcliente');
            })
            ->select([
                'movimento.idcliente as cliente_id',
                'movimento.idempenho as empenho_id',
                'empenho.exercicio',
                'empenho.numero',
                'empenho.dataemissao as data_emissao',
                'empenho.tipo',
            ])
            ->selectRaw("(SELECT nome FROM cadmunicipe WHERE id = empenho.idcredor AND idcliente = empenho.idcliente) as nome")
            // Cálculos...
            ->selectRaw("SUM(movimento.emissao) AS total_empenhado")
            ->selectRaw("SUM(movimento.anular) AS total_anulado")
            ->selectRaw("SUM(movimento.liquidacao) AS total_liquidado")
            ->selectRaw("SUM(movimento.pagamento) AS total_pago")
            ->selectRaw("SUM(movimento.emissao - movimento.anular) AS saldo_empenhado")
            ->selectRaw("SUM(movimento.emissao - movimento.anular - movimento.liquidacao) AS saldo_liquidar")
            ->selectRaw("SUM(movimento.liquidacao - movimento.pagamento) AS saldo_pagar")

            ->where('movimento.idcliente', $idCliente)
            ->where('contaDespesa.idelemento', $elementoId)
            ->where('empenho.exercicio', $exercicio)

            // ADICIONE TODOS ESTES CAMPOS NO GROUP BY:
            ->groupBy(
                'movimento.idcliente',
                'empenho.idcliente',
                'movimento.idempenho',
                'empenho.id',
                'empenho.exercicio',
                'empenho.numero',
                'empenho.dataemissao',
                'empenho.tipo',
                'empenho.idcredor'
            )
            ->orderBy('nome')
            ->orderBy('data_emissao')
            ->get();
    }

    public function findElementoDespesaById(int $id, int $exercicio, int $idCliente)
    {
        return DB::table('ctbelemento')
            ->select('id', 'estrutural', 'nome as descricao')
            ->where('id', $id)
            ->where('exercicio', $exercicio)
            ->where('idcliente', $idCliente)
            ->first();
    }

    public function findRecursoById(int $id, int $exercicio, int $idCliente)
    {
        return DB::table('ctbempenho as empenho')
            ->join('ctbrecursovinculado as recurso', function ($join) {
                $join->on('recurso.id', '=', 'empenho.idrecurso')
                    ->on('recurso.idcliente', '=', 'empenho.idcliente');
            })
            ->select(
                'recurso.id as id',
                'empenho.idcliente as cliente_id',
                'empenho.idrecurso as recurso_id',
                'empenho.exercicio as exercicio',
                'recurso.codigo as codigo',
                'recurso.nome as descricao'
            )
            ->distinct()
            ->where('empenho.idcliente', $idCliente)
            ->where('empenho.exercicio', $exercicio)
            ->where('empenho.idrecurso', $id)
            ->orderBy('empenho.idcliente')
            ->orderBy('empenho.exercicio')
            ->orderBy('recurso.codigo')
            ->first();
    }

    public function findEmpenhosByRecurso(int $id, int $exercicio, int $idCliente)
    {
        // Subquery para buscar o nome do munícipe (credor)
        $nomeCredorSub = DB::table('cadmunicipe as municipe')
            ->select('municipe.nome')
            ->whereColumn('municipe.id', 'empenho.idcredor')
            ->whereColumn('municipe.idcliente', 'empenho.idcliente')
            ->limit(1);

        return DB::table('ctbempenhomovimento as movimento')
            ->join('ctbempenho as empenho', function ($join) {
                $join->on('empenho.id', '=', 'movimento.idempenho')
                    ->on('empenho.idcliente', '=', 'movimento.idcliente');
            })
            ->select(
                'movimento.idcliente as cliente_id',
                'movimento.idempenho as empenho_id',
                'empenho.exercicio as exercicio',
                'empenho.numero as numero',
                'empenho.dataemissao as data_emissao',
                'empenho.tipo as tipo'
            )
            // Adiciona a subquery do nome como uma coluna
            ->selectSub($nomeCredorSub, 'nome')
            // Cálculos de agregação
            ->selectRaw('SUM(movimento.emissao) as total_empenhado')
            ->selectRaw('SUM(movimento.anular) as total_anulado')
            ->selectRaw('SUM(movimento.liquidacao) as total_liquidado')
            ->selectRaw('SUM(movimento.pagamento) as total_pago')
            ->selectRaw('SUM(movimento.emissao - movimento.anular) as saldo_empenhado')
            ->selectRaw('SUM(movimento.emissao - movimento.anular - movimento.liquidacao) as saldo_liquidar')
            ->selectRaw('SUM(movimento.liquidacao - movimento.pagamento) as saldo_pagar')
            ->where('movimento.idcliente', $idCliente)
            ->where('empenho.exercicio', $exercicio)
            ->where('empenho.idrecurso', $id)
            ->groupBy(
                'movimento.idcliente',
                'movimento.idempenho',
                'empenho.exercicio',
                'empenho.idcliente',
                'empenho.idcredor', // Necessário para a subquery se o banco for strict
                'empenho.numero',
                'empenho.dataemissao',
                'empenho.tipo'
            )
            ->orderBy('nome')
            ->orderBy('data_emissao')
            ->get();
    }

    public function listaRecursos(int $exercicio, int $idCliente)
    {
        return DB::table('ctbempenhomovimento as movimento')
            ->join('ctbempenho as empenho', function ($join) {
                $join->on('empenho.id', '=', 'movimento.idempenho')
                    ->on('empenho.idcliente', '=', 'movimento.idcliente');
            })
            ->join('ctbrecursovinculado as recurso', function ($join) {
                $join->on('recurso.id', '=', 'empenho.idrecurso')
                    ->on('recurso.idcliente', '=', 'empenho.idcliente');
            })
            ->select(
                'movimento.idcliente as cliente_id',
                'empenho.idrecurso as recurso_id',
                'empenho.exercicio as exercicio',
                'recurso.codigo as codigo',
                'recurso.nome as descricao'
            )
            ->selectRaw('SUM(movimento.emissao) as total_empenhado')
            ->selectRaw('SUM(movimento.anular) as total_anulado')
            ->selectRaw('SUM(movimento.liquidacao) as total_liquidado')
            ->selectRaw('SUM(movimento.pagamento) as total_pago')
            ->selectRaw('SUM(movimento.emissao - movimento.anular) as saldo_empenhado')
            ->selectRaw('SUM(movimento.emissao - movimento.anular - movimento.liquidacao) as saldo_liquidar')
            ->selectRaw('SUM(movimento.liquidacao - movimento.pagamento) as saldo_pagar')
            ->where('movimento.idcliente', $idCliente)
            ->where('empenho.exercicio', $exercicio)
            ->groupBy(
                'movimento.idcliente',
                'empenho.idrecurso',
                'empenho.exercicio',
                'recurso.codigo', // Adicionado para conformidade com SQL estrito
                'recurso.nome'   // Adicionado para conformidade com SQL estrito
            )
            ->orderBy('saldo_empenhado', 'DESC')
            ->get();
    }

    /**
     * Lista os Órgãos de um determinado exercício
     */
    public function listaOrgaos(int $exercicio, int $idCliente)
    {
        return DB::table('ctbempenhomovimento as movimento')
            ->join('ctbempenho as empenho', function ($j) {
                $j->on('empenho.id', '=', 'movimento.idempenho')
                    ->on('empenho.idcliente', '=', 'movimento.idcliente');
            })
            ->join('ctbcontadespesa as contaDespesa', function ($j) {
                $j->on('contaDespesa.id', '=', 'empenho.iddespesa')
                    ->on('contaDespesa.idcliente', '=', 'empenho.idcliente');
            })
            ->join('ctbunidadeorcamentaria as unidade', function ($j) {
                $j->on('unidade.id', '=', 'contaDespesa.idunidadeorcamentaria')
                    ->on('unidade.idcliente', '=', 'contaDespesa.idcliente');
            })
            ->join('ctborgao as orgao', function ($j) {
                $j->on('orgao.id', '=', 'unidade.idorgao')
                    ->on('orgao.idcliente', '=', 'unidade.idcliente');
            })
            ->select([
                'orgao.id as orgao_id',
                'orgao.nome as orgao_nome',
            ])
            // Agregações financeiras idênticas às que usamos nas outras telas
            ->selectRaw("SUM(movimento.emissao) AS total_empenhado")
            ->selectRaw("SUM(movimento.anular) AS total_anulado")
            ->selectRaw("SUM(movimento.liquidacao) AS total_liquidado")
            ->selectRaw("SUM(movimento.pagamento) AS total_pago")
            ->selectRaw("SUM(movimento.emissao - movimento.anular) AS saldo_empenhado")
            ->selectRaw("SUM(movimento.emissao - movimento.anular - movimento.liquidacao) AS saldo_liquidar")
            ->selectRaw("SUM(movimento.liquidacao - movimento.pagamento) AS saldo_pagar")

            ->where('movimento.idcliente', $idCliente)
            ->where('empenho.exercicio', $exercicio)

            ->groupBy('orgao.id', 'orgao.nome')
            ->orderBy('total_empenhado', 'DESC') // Ordena pelos maiores gastos
            ->get();
    }

    /**
     * Lista todos os empenhos vinculados a um Órgão específico em um exercício
     */
    public function findEmpenhosByOrgao(int $orgaoId, int $exercicio, int $idCliente)
    {
        return DB::table('ctbempenhomovimento as movimento')
            ->join('ctbempenho as empenho', function ($j) {
                $j->on('empenho.id', '=', 'movimento.idempenho')
                    ->on('empenho.idcliente', '=', 'movimento.idcliente');
            })
            ->join('ctbcontadespesa as contaDespesa', function ($j) {
                $j->on('contaDespesa.id', '=', 'empenho.iddespesa')
                    ->on('contaDespesa.idcliente', '=', 'empenho.idcliente');
            })
            ->join('ctbunidadeorcamentaria as unidade', function ($j) {
                $j->on('unidade.id', '=', 'contaDespesa.idunidadeorcamentaria')
                    ->on('unidade.idcliente', '=', 'contaDespesa.idcliente');
            })
            ->select([
                'movimento.idcliente as cliente_id',
                'movimento.idempenho as empenho_id',
                'empenho.exercicio',
                'empenho.numero',
                'empenho.dataemissao as data_emissao',
                'empenho.tipo',
            ])
            // Subquery para buscar o nome do Credor
            ->selectRaw("(SELECT nome FROM cadmunicipe WHERE id = empenho.idcredor AND idcliente = empenho.idcliente) as nome_credor")

            // Agregações Financeiras (Saldos conforme sua imagem)
            ->selectRaw("SUM(movimento.emissao - movimento.anular) AS saldo_empenhado")
            ->selectRaw("SUM(movimento.emissao - movimento.anular - movimento.liquidacao) AS saldo_liquidar")
            ->selectRaw("SUM(movimento.liquidacao - movimento.pagamento) AS saldo_pagar")

            ->where('movimento.idcliente', $idCliente)
            ->where('empenho.exercicio', $exercicio)
            ->where('unidade.idorgao', $orgaoId) // O filtro principal por Órgão

            ->groupBy(
                'movimento.idcliente',
                'empenho.idcliente',
                'movimento.idempenho',
                'empenho.id',
                'empenho.exercicio',
                'empenho.numero',
                'empenho.dataemissao',
                'empenho.tipo',
                'empenho.idcredor'
            )
            ->orderBy('empenho.dataemissao', 'DESC')
            ->orderBy('empenho.numero', 'DESC')
            ->get();
    }

    /**
     * Busca os dados de um Órgão específico por ID
     */
    public function findOrgaoById(int $id, int $exercicio, int $idCliente)
    {
        return DB::table('ctborgao as orgao')
            ->select([
                'orgao.id as id',
                'orgao.idcliente as cliente_id',
                'orgao.exercicio as exercicio',
                'orgao.codigo as codigo',
                'orgao.nome as descricao'
            ])
            ->where('orgao.id', $id)
            ->where('orgao.idcliente', $idCliente)
            ->where('orgao.exercicio', $exercicio)
            ->first(); // Retorna apenas um objeto ou null
    }
}
