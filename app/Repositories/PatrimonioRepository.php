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
        // 1. Dados Básicos do Patrimônio
        $patrimonio = DB::table('patpatrimonio as p')
            ->leftJoin('almproduto as prod', function ($j) {
                $j->on('prod.id', '=', 'p.idproduto')
                    ->on('prod.idcliente', '=', 'p.idcliente');
            })
            ->select('p.*', 'prod.nome as nome_produto')
            ->where('p.id', $idPatrimonio)
            ->where('p.idcliente', $idcliente)
            ->first();

        if (!$patrimonio) return null;

        // 2. Movimentações (Múltiplos registros)
        $movimentacoes = DB::table('patpatrimoniomovimento')
            ->where('idcliente', $idcliente)
            ->where('idpatrimonio', $idPatrimonio)
            ->orderBy('codigo')
            ->get();

        // 3. Dados de Baixa (Registro único)
        $baixa = DB::table('patpatrimoniobaixa')
            ->where('idcliente', $idcliente)
            ->where('idpatrimonio', $idPatrimonio)
            ->first();

        // 4. Dados de Veículo
        $veiculo = DB::table('patpatrimonioveiculo')
            ->where('idcliente', $idcliente)
            ->where('idpatrimonio', $idPatrimonio)
            ->first();

        // 5. Dados de Semovente
        $semovente = DB::table('patpatrimoniosemovente')
            ->where('idcliente', $idcliente)
            ->where('idpatrimonio', $idPatrimonio)
            ->first();

        return [
            'patrimonio'    => $patrimonio,
            'movimentacoes' => $movimentacoes,
            'baixa'         => $baixa,
            'veiculo'       => $veiculo,
            'semovente'     => $semovente
        ];
    }
}
