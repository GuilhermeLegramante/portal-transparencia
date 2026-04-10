<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class SessaoRepository
{
    public function getSessoes($idcliente, $exercicio, $tipo = null, $idLeg = null)
    {
        $query = DB::table('leisessao as sessao')
            ->join('leimesadiretora as mesaDiretora', function ($j) {
                $j->on('mesaDiretora.id', '=', 'sessao.idmesadiretora')->on('mesaDiretora.idcliente', '=', 'sessao.idcliente');
            })
            ->where('sessao.idcliente', $idcliente)
            ->whereYear('sessao.horario', $exercicio);

        if ($tipo) $query->where('sessao.tipo', $tipo);
        if ($idLeg) $query->where('mesaDiretora.idlegislatura', $idLeg);

        return $query->orderBy('sessao.numero', 'DESC')->get();
    }

    public function getDetalhesSessao($idcliente, $idSessao)
    {
        return [
            'sessao' => DB::table('leisessao')->where('id', $idSessao)->where('idcliente', $idcliente)->first(),
            'proposicoes' => DB::table('leisessaocronograma as c')
                ->join('leiproposicao as p', 'p.id', '=', 'c.idproposicao')
                ->select('p.*')
                ->selectRaw("(SELECT nome FROM cadmunicipe WHERE id = p.idautor) as nome_autor")
                ->where('c.idsessao', $idSessao)->get(),
            'correspondencias' => DB::table('leisessaocronograma as c')
                ->join('leitexto as t', 't.id', '=', 'c.idcorrespondencia')
                ->where('c.idsessao', $idSessao)->get(),
            'presencas' => DB::table('leisessaopresenca as presenca')
                ->select('presenca.*')
                ->selectRaw("(SELECT m.nome FROM leiparlamentar p INNER JOIN cadmunicipe m ON m.id = p.idmunicipe 
                              WHERE presenca.idparlamentar = p.id) as nome")
                ->where('presenca.idsessao', $idSessao)->get(),
            'votacoes' => DB::table('leivotacao as v')
                ->join('leiproposicao as p', 'p.id', '=', 'v.idproposicao')
                ->select('p.descricao', 'p.dataproposicao as data', 'v.situacao')
                ->selectRaw("SUM(v.ausente) as ausente, SUM(v.favoravel) as favoravel, 
                             SUM(v.contrario) as contrario, SUM(v.absteve) as abstencao")
                ->where('v.idsessao', $idSessao)
                ->groupBy('p.id', 'v.situacao')->get()
        ];
    }

    public function getDetalheProjeto($idcliente, $idProtocolo)
    {
        return [
            'protocolo' => DB::table('prtprotocolo')->where('id', $idProtocolo)->where('idcliente', $idcliente)->first(),
            'tramites' => DB::table('prtprotocolodespacho')
                ->where('idprotocolo', $idProtocolo)
                ->where('idcliente', $idcliente)
                ->orderBy('datahora', 'DESC')->get()
        ];
    }
}
