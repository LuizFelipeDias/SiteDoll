<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class AuthController extends Controller
{
    // POST /login
    public function login(Request $request): Response|JsonResponse
    {
        $data = $request->validate([
            'email'    => ['required','email'],
            'password' => ['required','string'],
            'remember' => ['boolean'],
        ]);

        $remember = (bool) ($data['remember'] ?? false);

        if (! Auth::attempt(
            ['email' => $data['email'], 'password' => $data['password']],
            $remember
        )) {
            return response()->json(['message' => 'Credenciais inválidas.'], 422);
        }

        // evita session fixation
        $request->session()->regenerate();

        return response()->noContent(); // 204
    }

    // GET /me
    public function me(Request $request): JsonResponse
    {
        $u = $request->user(); // guard 'auth'
        return response()->json([
            'id'         => $u->id,
            'nome'       => $u->nome,
            'email'      => $u->email,
            'telefone'   => $u->telefone,
            'created_at' => $u->created_at,
        ]);
    }

    // POST /logout
    public function logout(Request $request): Response
    {
        Auth::guard('auth')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->noContent();
    }
}
