<?php

namespace App\Http\Controllers;

use App\Repositories\RegistroPrecoRepository;
use Illuminate\Http\Request;

class RegistroPrecoController extends Controller
{
    protected $repository;
    private $idCliente;


    public function __construct(RegistroPrecoRepository $repository)
    {
        $this->repository = $repository;
        $this->idCliente = config('app.client_id');
    }

    public function index(Request $request)
    {
        $filtro = $request->query('busca');
        $itens = $this->repository->getItensRegistrados($this->idCliente, $filtro);

        return view('compras.registro-preco.index', compact('itens'));
    }

    public function show($codigo)
    {
        $fornecedores = $this->repository->getFornecedoresPorItem($this->idCliente, $codigo);
        $item = $fornecedores->first(); // Para pegar o cabeçalho

        return view('compras.registro-preco.show', compact('fornecedores', 'item'));
    }
}
