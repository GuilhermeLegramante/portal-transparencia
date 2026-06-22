<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SicAuthMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->has('sic_user_id')) {
            return redirect()
                ->route('sic.login')
                ->with('error', 'Você precisa entrar no sistema para acessar esta área.');
        }

        return $next($request);
    }
}
