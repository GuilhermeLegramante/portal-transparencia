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
            'mes'       => $dados['mes'] ?? null, // Adicionado por prevenção
            'descricao' => mb_strtoupper($dados['descricao'], 'UTF-8'),
            'datahora'  => $dados['datahora'],
            'categoria' => mb_strtoupper($dados['categoria_texto'], 'UTF-8'),
            'path'      => $dados['path']
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
                'path'      => $dados['path']
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
}
