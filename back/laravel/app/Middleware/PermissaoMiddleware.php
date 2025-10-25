<?php

namespace App\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissaoMiddleware
{
    /**
     * Ex.: ->middleware('permissao:admin|editor')
     */
    public function handle(Request $request, Closure $next, ?string $permissao = null): Response|JsonResponse
    {
        $user = $request->user(); // funciona com auth:sanctum

        if (! $user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $papelDoUsuario = $user->permissao ?? $user->tipo ?? null;

        if ($permissao === null || $permissao === '') {
            return $next($request);
        }

        $exigidas = array_filter(explode('|', $permissao));

        if (! in_array($papelDoUsuario, $exigidas, true)) {
            return response()->json(['message' => 'Permissão negada'], 403);
        }

        return $next($request);
    }
}
