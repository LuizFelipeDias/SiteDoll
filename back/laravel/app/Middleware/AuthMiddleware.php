<?php

namespace App\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AuthMiddleware
{
    public function handle(Request $request, Closure $next): Response|JsonResponse
    {
        if (Auth::check()) {
            return $next($request);
        }

        return response()->json(['message' => 'Usuário não autenticado'], 401);
    }
}
