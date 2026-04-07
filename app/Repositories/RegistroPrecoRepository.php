<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class RegistroPrecoRepository
{
    public function getItensRegistrados($idcliente, $filtro = null)
    {
        // Criamos a query base
        $query = DB::table('comregistropreco as registroPreco')
            ->select(
                'registroPreco.idcliente as cliente_id',
                'registroPreco.validade as data_validade',
                'registroPreco.quantminima as quantidade_minima',
                'registroPreco.quantmaxima as quantidade_maxima',
                'registroPreco.situacaoitem as registro_ativo',
                DB::raw("IF(registroPreco.idproduto IS NOT NULL, 'P', 'S') AS tipo_registro"),
                DB::raw("IF(registroPreco.idproduto IS NOT NULL,
                CONCAT('P', registroPreco.idproduto),
                CONCAT('S', registroPreco.idservico)) AS item_codigo"),
                // Subquery da descrição
                DB::raw("IF(registroPreco.idproduto IS NOT NULL,
                (SELECT produto.nome FROM almproduto produto WHERE registroPreco.idproduto = produto.id AND registroPreco.idcliente = produto.idcliente),
                (SELECT servico.nome FROM comservico servico WHERE registroPreco.idservico = servico.id AND registroPreco.idcliente = servico.idcliente)) AS item_descricao")
            )
            ->distinct()
            ->where('registroPreco.idcliente', $idcliente)
            ->where('registroPreco.idfornecedor', '<>', -1);

        // Se houver filtro, usamos whereRaw repetindo a lógica da subquery para evitar o erro de HAVING
        if ($filtro) {
            $query->where(function ($q) use ($filtro) {
                $q->whereRaw("IF(registroPreco.idproduto IS NOT NULL,
                (SELECT p.nome FROM almproduto p WHERE registroPreco.idproduto = p.id AND registroPreco.idcliente = p.idcliente),
                (SELECT s.nome FROM comservico s WHERE registroPreco.idservico = s.id AND registroPreco.idcliente = s.idcliente)) LIKE ?", ["%{$filtro}%"]);
            });
        }

        return $query->orderBy('cliente_id')
            ->orderBy('data_validade', 'DESC')
            ->orderBy('item_codigo')
            ->get();
    }

    public function getFornecedoresPorItem($idcliente, $codigo)
    {
        // Identifica se o código começa com P ou S para filtrar no WHERE
        $tipo = substr($codigo, 0, 1); // 'P' ou 'S'
        $idOriginal = substr($codigo, 1); // O ID numérico

        return DB::table('comregistropreco as rp')
            ->select(
                'rp.idcliente as cliente_id',
                'rp.idfornecedor as fornecedor_id',
                'rp.classificacao',
                'rp.valorunitario as valor_unitario',
                'rp.situacaofornecedor as fornecedor_ativo',
                // Mantemos o alias apenas para exibição no Blade
                DB::raw("IF(rp.idproduto IS NOT NULL, CONCAT('P', rp.idproduto), CONCAT('S', rp.idservico)) AS item_codigo"),
                DB::raw("(SELECT m.nome FROM cadmunicipe m WHERE rp.idfornecedor = m.id AND rp.idcliente = m.idcliente) AS fornecedor"),
                DB::raw("IF(rp.idproduto IS NOT NULL, 
                (SELECT p.nome FROM almproduto p WHERE rp.idproduto = p.id AND rp.idcliente = p.idcliente),
                (SELECT s.nome FROM comservico s WHERE rp.idservico = s.id AND rp.idcliente = s.idcliente)) AS descricao_item")
            )
            ->where('rp.idcliente', $idcliente)
            ->where(function ($q) use ($tipo, $idOriginal) {
                if ($tipo === 'P') {
                    $q->where('rp.idproduto', $idOriginal);
                } else {
                    $q->where('rp.idservico', $idOriginal);
                }
            })
            ->orderBy('rp.classificacao', 'ASC')
            ->get();
    }
}
