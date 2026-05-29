<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Exibe a tela de login
     */
    public function showLoginForm()
    {
        // Se já estiver logado, redireciona para a home
        if (Auth::check()) {
            return redirect()->route('home');
        }

        return view('auth.login');
    }

    /**
     * Processa a requisição de login
     */
    public function login(Request $request)
    {
        // 1. Valida se a senha foi digitada
        $request->validate([
            'senha' => 'required|string',
        ], [
            'senha.required' => 'O campo senha é obrigatório.',
        ]);

        $clientName = config('app.client_name');

        // Criptografa a senha digitada em SHA-1 para bater com o banco de dados
        $senhaSha1 = sha1($request->input('senha'));

        // 2. Busca o cliente na tabela 'glbcliente' onde o identificador bate e a senha bate
        $cliente = DB::table('glbcliente')
            ->where('identificador', $clientName)
            ->where('senha', $senhaSha1)
            ->first();

        dd($clientName, $senhaSha1, $cliente);

        // 3. Se encontrou o registro correspondente
        if ($cliente) {

            // Força a autenticação na sessão do Laravel usando o ID do registro encontrado.
            // Nota: Se a sua chave primária não for 'id' (ex: 'idcliente'), ajuste para $cliente->idcliente
            $userId = $cliente->id ?? $cliente->idcliente ?? null;

            if ($userId) {
                Auth::loginUsingId($userId);

                // Regenera a sessão por segurança (evita fixation attacks)
                $request->session()->regenerate();

                // Redireciona o usuário para onde ele tentou ir ou para a página principal
                return redirect()->intended(route('home'));
            }
        }

        // 4. Se falhar, retorna com erro de credenciais inválidas
        return redirect()->back()
            ->withInput($request->only('senha'))
            ->withErrors([
                'login_error' => 'A senha informada está incorreta para este portal.',
            ]);
    }

    /**
     * Desconecta o usuário
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
