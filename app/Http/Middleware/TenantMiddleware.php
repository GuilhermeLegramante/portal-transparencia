<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class TenantMiddleware
{
    public function handle($request, Closure $next)
    {
        // Pega o host (ex: transparencia-cacequipm.hardsoftsistemas.com)
        $host = $request->getHost();

        // Extrai o identificador (pega o que está entre o primeiro '-' e o primeiro '.')
        // Lógica: transparencia-IDENTIFICADOR.dominio.com
        preg_match('/-(.*?)\./', $host, $matches);
        $identificador = $matches[1] ?? null;

        if (!$identificador) {
            abort(404, "Cliente não identificado.");
        }

        // Busca o cliente no banco global
        $cliente = DB::table('glbcliente')
            ->join('glbclientedado', 'glbcliente.id', '=', 'glbclientedado.idcliente')
            ->join('cadlogradouro', 'cadlogradouro.id', '=', 'glbclientedado.idlogradouro')
            ->join('cadbairro', 'cadbairro.id', '=', 'glbclientedado.idbairro')
            ->join('cadmunicipio', 'cadmunicipio.id', '=', 'cadbairro.idmunicipio')
            ->join('caduf', 'caduf.id', '=', 'cadmunicipio.iduf')
            ->select(
                'glbcliente.id',
                'glbcliente.identificador',
                'glbcliente.nome',
                'glbclientedado.cnpj',
                'glbclientedado.email',
                'glbclientedado.telefone',
                'glbclientedado.numero',
                'glbclientedado.website AS site',
                'glbclientedado.funcionamento',
                'cadlogradouro.nome AS logradouro',
                'cadlogradouro.cep AS cep',
                'cadbairro.nome AS bairro',
                'cadmunicipio.nome AS municipio',
                'caduf.sigla AS uf'
            )
            ->where('identificador', $identificador)->first();


        if (!$cliente) {
            abort(404, "Órgão não encontrado.");
        }

        // Sobrescreve as configurações do config/app.php em tempo de execução
        Config::set('app.client_id', $cliente->id);
        Config::set('app.client_name', $cliente->identificador);
        Config::set('app.client_full_name', $cliente->nome);
        Config::set('app.client_cnpj', $cliente->cnpj);
        Config::set('app.client_email', $cliente->email);
        Config::set('app.phone', $cliente->telefone);
        Config::set('app.client_address', $cliente->logradouro . ', ' .
            $cliente->numero . ' - ' . $cliente->bairro . ', ' .
            $cliente->municipio . ' - ' . $cliente->uf . ', ' .
            $cliente->cep);
        Config::set('app.client_city', $cliente->municipio);
        Config::set('app.client_state', $cliente->uf);
        Config::set('app.client_site', $cliente->site);
        Config::set('app.client_operation_hours', $cliente->funcionamento);

        $updatedAt = DB::table('glbclientepublicacao')
            ->where('idcliente', $cliente->id)
            ->max('datahora');

        Config::set('app.client_updated_at', $updatedAt ? date('Y-m-d H:i:s', strtotime($updatedAt)) : null);

        return $next($request);
    }
}
