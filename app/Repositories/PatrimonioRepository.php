<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class PatrimonioRepository
{
    public function getPatrimonios($idcliente, $filtros = [])
    {
        $query = DB::table('patpatrimonio as patrimonio')
            ->select(
                'patrimonio.idcliente as cliente_id',
                'patrimonio.id as patrimonio_id',
                DB::raw('CAST(patrimonio.numero AS UNSIGNED) AS numero'),
                'patrimonio.datainicio as data_inicio',
                'patrimonio.valorentrada as valor_compra',
                'patrimonio.situacao',
                'patrimonio.complemento'
            )
            ->selectRaw("(SELECT CONCAT(produto.nome, ' - ', patrimonio.complemento)
                          FROM almproduto produto
                         WHERE patrimonio.idproduto = produto.id
                           AND patrimonio.idcliente = produto.idcliente) AS descricao")
            ->where('patrimonio.idcliente', $idcliente)
            ->where('patrimonio.classe', '<>', '')
            ->where('patrimonio.especie', '<>', '')
            ->where('patrimonio.classificacao', '<>', '');

        if (!empty($filtros['classe'])) $query->where('patrimonio.classe', $filtros['classe']);
        if (!empty($filtros['especie'])) $query->where('patrimonio.especie', $filtros['especie']);
        if (!empty($filtros['classificacao'])) $query->where('patrimonio.classificacao', $filtros['classificacao']);

        return $query->orderBy('numero')->get();
    }

    public function getFiltros($idcliente)
    {
        return [
            'especies' => DB::table('patpatrimonio')->where('idcliente', $idcliente)->distinct()->orderBy('especie')->pluck('especie'),
            'classificacoes' => DB::table('patpatrimonio')->where('idcliente', $idcliente)->distinct()->orderBy('classificacao')->pluck('classificacao'),
            'classes' => ['Mobiliário', 'Veículo', 'Intangível', 'Esgotável', 'Construção', 'Terreno', 'Semovente']
        ];
    }

    public function getDetalhes($idcliente, $idPatrimonio)
    {
        // 1. Dados Cabeçalho (Patrimônio)
        $dados = DB::table('patpatrimonio as p')
            ->leftJoin('almproduto as prod', function ($j) {
                $j->on('prod.id', '=', 'p.idproduto')->on('prod.idcliente', '=', 'p.idcliente');
            })
            ->select('p.*', 'prod.nome as nome_produto')
            ->where('p.id', $idPatrimonio)
            ->where('p.idcliente', $idcliente)
            ->first();

        if (!$dados) return null;

        return [
            'patrimonio' => $dados,
            // 'movimentacoes' => DB::table('patmovimentacao')->where('idpatrimonio', $idPatrimonio)->where('idcliente', $idcliente)->get(),
            // 'baixa' => DB::table('patbaixa')->where('idpatrimonio', $idPatrimonio)->where('idcliente', $idcliente)->first(),
            // 'veiculo' => DB::table('patveiculo')->where('idpatrimonio', $idPatrimonio)->where('idcliente', $idcliente)->first(),
            // 'semovente' => DB::table('patsemovente')->where('idpatrimonio', $idPatrimonio)->where('idcliente', $idcliente)->first(),
        ];
    }
}
