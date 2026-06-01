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
     * Exclui o registro na tabela exata (pubpublicacao ou pubprestacaoconta) 
     * evitando colisões de IDs idênticos vindos do UNION.
     */
    public function excluirPublicacaoPorTipo($id, $idcliente, $tipo)
    {
        return DB::transaction(function () use ($id, $idcliente, $tipo) {

            // CENÁRIO A: É uma publicação geral
            if ($tipo === 'geral') {
                $registro = DB::table('pubpublicacao')
                    ->where('id', $id)
                    ->where('idcliente', $idcliente)
                    ->first();

                if ($registro) {
                    // 1. Limpa os vínculos das tags na tabela pivot primeiro (pubpublicacaotag)
                    DB::table('pubpublicacaotag')
                        ->where('idpublicacao', $id)
                        ->where('idcliente', $idcliente)
                        ->delete();

                    // 2. Deleta o registro principal da tabela pubpublicacao
                    DB::table('pubpublicacao')
                        ->where('id', $id)
                        ->where('idcliente', $idcliente)
                        ->delete();

                    return $registro; // Retorna o objeto antigo para a Controller apagar o PDF
                }
            }

            // CENÁRIO B: É uma prestação de contas
            if ($tipo === 'prestacao') {
                $registroPrestacao = DB::table('pubprestacaoconta')
                    ->where('id', $id)
                    ->where('idcliente', $idcliente)
                    ->first();

                if ($registroPrestacao) {
                    // Deleta o registro direto da tabela pubprestacaoconta
                    DB::table('pubprestacaoconta')
                        ->where('id', $id)
                        ->where('idcliente', $idcliente)
                        ->delete();

                    return $registroPrestacao; // Retorna o objeto antigo para a Controller apagar o PDF
                }
            }

            return null; // Caso não encontre em nenhum dos fluxos
        });
    }
}
