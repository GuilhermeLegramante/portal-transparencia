<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class ParlamentarRepository
{
    public function getLegislaturas($idcliente)
    {
        return DB::table('leilegislatura')
            ->where('idcliente', $idcliente)
            ->get();
    }

    public function getParlamentaresPorLegislatura($idcliente, $idLeg)
    {
        return DB::table('leimandato as mandato')
            ->select('mandato.*')
            ->selectRaw("(SELECT municipe.nome FROM leiparlamentar parlamentar 
                          INNER JOIN cadmunicipe municipe ON municipe.id = parlamentar.idmunicipe 
                          AND municipe.idcliente = parlamentar.idcliente 
                          WHERE mandato.idparlamentar = parlamentar.id 
                          AND mandato.idcliente = parlamentar.idcliente) as nome")
            ->where('mandato.idcliente', $idcliente)
            ->where('mandato.idlegislatura', $idLeg)
            ->orderBy('voto', 'DESC')
            ->get();
    }

    public function getMesaDiretora($idcliente, $idLeg)
    {
        return DB::table('leimesaparlamentar as mesaParlamentar')
            ->join('leimesadiretora as mesaDiretora', function ($join) {
                $join->on('mesaDiretora.id', '=', 'mesaParlamentar.idmesadiretora')
                    ->on('mesaDiretora.idcliente', '=', 'mesaParlamentar.idcliente');
            })
            ->select('mesaParlamentar.*', 'mesaDiretora.descricao as mesa_diretora')
            ->selectRaw("(SELECT municipe.nome FROM leiparlamentar parlamentar 
                          INNER JOIN cadmunicipe municipe ON municipe.id = parlamentar.idmunicipe 
                          WHERE mesaParlamentar.idparlamentar = parlamentar.id) as nome")
            ->selectRaw("(SELECT h.nome FROM cadcomissaohierarquia h WHERE mesaParlamentar.idhierarquia = h.id) as cargo")
            ->selectRaw("(SELECT m.partido FROM leimandato m WHERE m.idparlamentar = mesaParlamentar.idparlamentar 
                          AND m.idlegislatura = mesaDiretora.idlegislatura) as partido")
            ->where('mesaParlamentar.idcliente', $idcliente)
            ->where('mesaDiretora.idlegislatura', $idLeg)
            ->orderBy('cargo', 'DESC')
            ->get()
            ->groupBy('mesa_diretora');
    }
}
