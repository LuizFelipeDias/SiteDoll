<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;

class LoginController extends Controller
{
    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $request->authenticate();           // Auth::attempt(...) + rate limit
        $request->session()->regenerate();  // evita fixation

        // sem passar 200 explicitamente (é o default) -> evita aviso "argument matches default value"
        return response()->json([
            'message' => 'Login realizado',
            'user'    => $request->user(),  // App\Models\Usuario
        ]);
    }
}
