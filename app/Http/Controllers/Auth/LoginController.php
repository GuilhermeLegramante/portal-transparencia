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
        $request->validate([
            'senha' => 'required|string',
        ], [
            'senha.required' => 'O campo senha é obrigatório.',
        ]);

        $clientName = config('app.client_name'); // 'cacequicm'
        $senhaSha1 = sha1($request->input('senha'));

        // Usando LIKE binário ou comparando limpando espaços (TRIM) para forçar o banco a ignorar qualquer metadado ou espaço invisível
        $cliente = DB::table('glbcliente')
            ->whereRaw('TRIM(identificador) LIKE ?', [trim($clientName)])
            ->whereRaw('TRIM(senha) LIKE ?', [trim($senhaSha1)])
            ->first();

        dd($cliente);

        if ($cliente) {
            // Valida se o cliente está ativo (no seu print, Cacequi está ativo = 1)
            if ($cliente->ativo == 0) {
                return redirect()->back()
                    ->withErrors(['login_error' => 'Este portal encontra-se inativo no momento.']);
            }

            // Realiza o login utilizando o ID correto (ID 3 do seu print)
            Auth::loginUsingId($cliente->id);

            $request->session()->regenerate();

            return redirect()->intended(route('home'));
        }

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
