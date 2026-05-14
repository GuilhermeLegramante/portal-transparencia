<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cache;

class TenantMiddleware
{
    /**
     * Trata a requisição para identificar o cliente (tenant) via subdomínio.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // 1. Extrair o identificador da URL (ex: transparencia-cacequipm.dominio.com)
        $host = $request->getHost();
        preg_match('/-(.*?)\./', $host, $matches);
        $identificador = $matches[1] ?? null;

        if (!$identificador) {
            abort(404, "Identificador do órgão não encontrado na URL.");
        }

        // 2. Buscar dados do cliente com Cache (expira em 24 horas)
        // O cache é único por identificador para não misturar dados de prefeituras
        $cliente = Cache::remember("tenant_data_{$identificador}", 86400, function () use ($identificador) {
            return DB::table('glbcliente')
                ->join('glbclientedado', 'glbclientedado.idcliente', '=', 'glbcliente.id')
                ->join('cadlogradouro', function ($join) {
                    $join->on('cadlogradouro.id', '=', 'glbclientedado.idlogradouro')
                         ->on('cadlogradouro.idcliente', '=', 'glbcliente.id');
                })
                ->join('cadbairro', function ($join) {
                    $join->on('cadbairro.id', '=', 'glbclientedado.idbairro')
                         ->on('cadbairro.idcliente', '=', 'glbcliente.id');
                })
                ->join('cadmunicipio', function ($join) {
                    $join->on('cadmunicipio.id', '=', 'cadbairro.idmunicipio')
                         ->on('cadmunicipio.idcliente', '=', 'glbcliente.id');
                })
                ->join('caduf', function ($join) {
                    $join->on('caduf.id', '=', 'cadmunicipio.iduf')
                         ->on('caduf.idcliente', '=', 'glbcliente.id');
                })
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
                ->where('glbcliente.identificador', $identificador)
                ->first();
        });

        if (!$cliente) {
            abort(404, "Órgão/Prefeitura não encontrada para o identificador: " . $identificador);
        }

        // 3. Injetar as configurações globalmente no sistema
        Config::set('app.client_id', $cliente->id);
        Config::set('app.client_name', $cliente->identificador);
        Config::set('app.client_full_name', $cliente->nome);
        Config::set('app.client_cnpj', $cliente->cnpj);
        Config::set('app.client_email', $cliente->email);
        Config::set('app.client_phone', $cliente->telefone);
        Config::set('app.client_site', $cliente->site);
        Config::set('app.client_operation_hours', $cliente->funcionamento);
        
        // Endereço formatado
        $enderecoCompleto = "{$cliente->logradouro}, {$cliente->numero} - {$cliente->bairro}, {$cliente->municipio} - {$cliente->uf}, CEP: {$cliente->cep}";
        Config::set('app.client_address', $enderecoCompleto);
        Config::set('app.client_city', $cliente->municipio);
        Config::set('app.client_state', $cliente->uf);

        // 4. Buscar data da última atualização (Cache de 10 minutos)
        $updatedAt = Cache::remember("tenant_last_update_{$cliente->id}", 600, function () use ($cliente) {
            return DB::table('glbclientepublicacao')
                ->where('idcliente', $cliente->id)
                ->max('datahora');
        });

        Config::set('app.client_updated_at', $updatedAt ? date('d/m/Y', strtotime($updatedAt)) : 'N/A');

        return $next($request);
    }
}