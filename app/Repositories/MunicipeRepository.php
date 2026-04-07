<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class MunicipeRepository
{
    /**
     * Busca os dados detalhados de um munícipe/credor.
     */
    public function findById(int $id, int $idCliente)
    {
        return DB::table('cadmunicipe as municipe')
            ->leftJoin('cadbairro as bairro', function ($join) {
                $join->on('bairro.id', '=', 'municipe.idbairro')
                    ->on('bairro.idcliente', '=', 'municipe.idcliente');
            })
            ->leftJoin('cadlogradouro as logradouro', function ($join) {
                $join->on('logradouro.id', '=', 'municipe.idlogradouro')
                    ->on('logradouro.idcliente', '=', 'municipe.idcliente');
            })
            ->leftJoin('cadmunicipio as municipio', function ($join) {
                $join->on('municipio.id', '=', 'logradouro.idmunicipio')
                    ->on('municipio.idcliente', '=', 'logradouro.idcliente');
            })
            ->leftJoin('caduf as uf', function ($join) {
                $join->on('uf.id', '=', 'municipio.iduf')
                    ->on('uf.idcliente', '=', 'municipio.idcliente');
            })
            ->select(
                'municipe.id as inscricao',
                'municipe.nome',
                'municipe.tipopessoa as tipo_pessoa',
                'municipe.cpf',
                'municipe.cnpj',
                'municipe.telefone',
                'municipe.celular',
                'municipe.email',
                'bairro.nome as nome_bairro',
                'logradouro.nome as nome_logradouro',
                'logradouro.cep',
                'municipe.numero as numero_imovel',
                'municipio.nome as nome_municipio',
                'uf.sigla as uf'
            )
            ->where('municipe.idcliente', $idCliente)
            ->where('municipe.id', $id)
            ->first();
    }
}
