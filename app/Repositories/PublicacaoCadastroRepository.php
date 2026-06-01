<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class PublicacaoCadastroRepository
{
    public function salvarPrestacaoContas($dados)
    {
        return DB::table('pubprestacaoconta')->insertGetId([
            'idcliente' => $dados['idcliente'],
            'exercicio' => $dados['exercicio'],
            // 'mes'       => $dados['mes'] ?? null, // Adicionado por prevenção
            'descricao' => mb_strtoupper($dados['descricao'], 'UTF-8'),
            'datahora'  => $dados['datahora'],
            'categoria' => mb_strtoupper($dados['categoria_texto'], 'UTF-8'),
            'path'      => $dados['path'],
        ]);
    }

    public function salvarPublicacaoGeral($dados, $tagsIds)
    {
        return DB::transaction(function () use ($dados, $tagsIds) {
            // 1. Insere na tabela principal de publicações (Incluindo o campo 'mes')
            $idPublicacao = DB::table('pubpublicacao')->insertGetId([
                'idcliente' => $dados['idcliente'],
                'exercicio' => $dados['exercicio'],
                'mes'       => $dados['mes'], // <--- RESOLUÇÃO DO ERRO AQUI
                'descricao' => mb_strtoupper($dados['descricao'], 'UTF-8'),
                'datahora'  => $dados['datahora'],
                'path'      => $dados['path'],
                'views'     => 0
            ]);

            // 2. Vincula as Tags selecionadas na tabela pivot (pubpublicacaotag)
            if (!empty($tagsIds)) {
                $vinculos = [];
                foreach ($tagsIds as $idTag) {
                    $vinculos[] = [
                        'idcliente'    => $dados['idcliente'],
                        'idpublicacao' => $idPublicacao,
                        'idtag'        => $idTag
                    ];
                }
                DB::table('pubpublicacaotag')->insert($vinculos);
            }

            return $idPublicacao;
        });
    }

    /**
     * Retorna todas as tags globais cadastradas no sistema para o usuário selecionar
     */
    public function getTagsGlobais()
    {
        return DB::table('pubtag')->orderBy('nome', 'asc')->get();
    }

    /**
     * Remove a publicação (Geral ou Prestação de Contas) e limpa seus vínculos
     */
    public function excluirPublicacaoDinamica($codigo, $idcliente)
    {
        return DB::transaction(function () use ($codigo, $idcliente) {
            // 1. Tenta buscar primeiro na tabela de Publicação Geral
            $registro = DB::table('pubpublicacao')
                ->where('codigo', $codigo)
                ->where('idcliente', $idcliente)
                ->first();

            if ($registro) {
                // Remove os vínculos das tags na tabela pivot
                DB::table('pubpublicacaotag')
                    ->where('idpublicacao', $registro->id)
                    ->where('idcliente', $idcliente)
                    ->delete();

                // Deleta da tabela principal
                DB::table('pubpublicacao')
                    ->where('codigo', $codigo)
                    ->where('idcliente', $idcliente)
                    ->delete();

                return $registro;
            }

            // 2. Se não encontrou na Geral, busca na tabela de Prestação de Contas
            $registroPrestacao = DB::table('pubprestacaoconta')
                ->where('codigo', $codigo)
                ->where('idcliente', $idcliente)
                ->first();

            if ($registroPrestacao) {
                DB::table('pubprestacaoconta')
                    ->where('codigo', $codigo)
                    ->where('idcliente', $idcliente)
                    ->delete();

                return $registroPrestacao;
            }

            return null; // Não encontrou em nenhuma das tabelas
        });
    }
}
