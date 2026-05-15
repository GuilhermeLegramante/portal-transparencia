<?php

namespace App\Http\Controllers;

use App\Repositories\CronogramaPagamentoRepository;
use Illuminate\Http\Request;

class CronogramaPagamentoController extends Controller
{
    protected $repo;
    private $idCliente;

    public function __construct(CronogramaPagamentoRepository $repo)
    {
        $this->repo = $repo;
        
    }

    public function index(Request $request)
    {
        $exercicio = $request->get('exercicio', date('Y'));
        $credorId = $request->get('credor_id'); // Opcional
        $recursoId = $request->get('recurso_id'); // Opcional

        $resumoRecursos = $this->repo->getResumoPorRecurso(config('app.client_id'), $exercicio);
        $pagamentos = $this->repo->getListagemCompleta(config('app.client_id'), $exercicio, $credorId, $recursoId);

        $breadcrumb = [
            'Finanças' => '#',
            'Cronograma de Pagamento' => ''
        ];

        return view('financas.cronograma.index', compact(
            'resumoRecursos',
            'pagamentos',
            'exercicio',
            'breadcrumb'
        ));
    }
}
