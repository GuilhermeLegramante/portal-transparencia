<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SicController extends Controller
{
    private $idCliente;

    public function __construct()
    {
        // O idCliente já vem configurado pelo seu TenantMiddleware
        $this->idCliente = config('app.client_id');
    }

    public function index()
    {
        $breadcrumb = [
            'Início' => route('home'),
            'SIC - Serviço de Informação ao Cidadão' => ''
        ];

        // Dados dinâmicos para a página inicial (Exemplo de estatística rápida)
        $totalPedidos = DB::table('sicpedido') // Ajuste o nome da sua tabela de pedidos
            ->where('idcliente', $this->idCliente)
            ->count();

        return view('sic.index', compact('breadcrumb', 'totalPedidos'));
    }

    public function contato()
    {
        $breadcrumb = [
            'SIC' => route('sic.index'),
            'Fale Conosco' => ''
        ];
        return view('sic.contato', compact('breadcrumb'));
    }

    public function enviarEmail(Request $request)
    {
        $dados = $request->validate([
            'nome' => 'required',
            'email' => 'required|email',
            'assunto' => 'required',
            'mensagem' => 'required'
        ]);

        // O destino é o e-mail do cliente configurado no Middleware
        $destino = config('app.client_email');

        // Aqui você dispararia o Mail::send...
        // Mail::to($destino)->send(new SicContatoMail($dados));

        return back()->with('success', 'Sua mensagem foi enviada com sucesso! Em breve retornaremos.');
    }

    public function estatisticas(Request $request)
    {
        $exercicio = $request->get('exercicio', date('Y'));

        $breadcrumb = [
            'SIC' => route('sic.index'),
            'Consulta de Solicitações' => ''
        ];

        $pedidos = DB::table('sicpedido as pedido')
            ->join('sicusuario as usuario', function ($join) {
                $join->on('usuario.id', '=', 'pedido.idusuario')
                    ->on('pedido.idcliente', '=', 'usuario.idcliente');
            })
            ->select(
                'pedido.datahora',
                'usuario.nome as solicitante',
                'usuario.tipopessoa',
                'pedido.titulo as descricao',
                'pedido.situacao',
                'pedido.avaliacao'
            )
            ->where('pedido.idcliente', $this->idCliente)
            ->whereYear('pedido.datahora', $exercicio)
            ->orderBy('pedido.datahora', 'desc')
            ->get();

        return view('sic.estatisticas', compact('pedidos', 'exercicio', 'breadcrumb'));
    }

    /**
     * Exibe a tela de login
     */
    public function login()
    {
        $breadcrumb = [
            'SIC' => route('sic.index'),
            'Acesso ao Sistema' => ''
        ];

        return view('sic.login', compact('breadcrumb'));
    }

    /**
     * Exibe a tela de cadastro
     */
    public function cadastro()
    {
        $breadcrumb = [
            'SIC' => route('sic.index'),
            'Cadastro de Cidadão' => ''
        ];

        return view('sic.cadastro', compact('breadcrumb'));
    }

    /**
     * Processa o registro do novo cidadão
     */
    public function storeCadastro(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8|confirmed',
            'tipopessoa' => 'required|in:F,J',
            'documento' => 'required|string',
        ]);

        // Verifica se o e-mail já existe para ESTE cliente específico
        $usuarioExistente = DB::table('sicusuario')
            ->where('email', $request->email)
            ->where('idcliente', $this->idCliente)
            ->first();

        if ($usuarioExistente) {
            return back()->withErrors(['email' => 'Este e-mail já está cadastrado para este órgão.']);
        }

        // Insere no banco usando a tabela que você enviou no SQL
        DB::table('sicusuario')->insert([
            'idcliente'  => $this->idCliente,
            'nome'       => $request->name,
            'email'      => $request->email,
            'senha'      => Hash::make($request->password), // Sempre criptografe a senha
            'tipopessoa' => $request->tipopessoa,
            'documento'  => preg_replace('/[^0-9]/', '', $request->documento), // Remove pontos e traços
            'created_at' => now(),
        ]);

        return redirect()->route('sic.login')->with('success', 'Cadastro realizado com sucesso! Faça login para continuar.');
    }

    /**
     * Processa o Login (Exemplo simplificado)
     */
    public function autenticar(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Busca o usuário garantindo que ele pertence ao cliente atual (Multitenant)
        $user = DB::table('sicusuario')
            ->where('email', $credentials['email'])
            ->where('idcliente', $this->idCliente)
            ->first();

        if ($user && Hash::check($credentials['password'], $user->senha)) {
            // Se você usar o Auth do Laravel, precisará configurar um Guard específico.
            // Para um exemplo rápido, podemos salvar na sessão:
            session(['sic_user_id' => $user->id]);
            session(['sic_user_name' => $user->nome]);

            return redirect()->route('sic.index')->with('success', 'Bem-vindo ao sistema SIC!');
        }

        return back()->withErrors(['email' => 'As credenciais não conferem para este órgão.']);
    }

    public function logout()
    {
        session()->forget(['sic_user_id', 'sic_user_name']);
        return redirect()->route('sic.index');
    }
}
