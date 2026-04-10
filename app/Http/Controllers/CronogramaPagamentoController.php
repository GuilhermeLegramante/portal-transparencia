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
        $this->idCliente = config('app.client_id');
    }

    public function index(Request $request)
    {
        $exercicio = $request->get('exercicio', date('Y'));
        $credorId = $request->get('credor_id'); // Opcional
        $recursoId = $request->get('recurso_id'); // Opcional

        $resumoRecursos = $this->repo->getResumoPorRecurso($this->idCliente, $exercicio);
        $pagamentos = $this->repo->getListagemCompleta($this->idCliente, $exercicio, $credorId, $recursoId);

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
