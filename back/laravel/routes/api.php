<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\TipoController;
use App\Http\Controllers\PromptController;

Route::post('/cadastro', [UsuarioController::class, 'store']);

Route::post('/auth/login-token',   [AuthController::class, 'loginToken']);
Route::post('/auth/login-session', [AuthController::class, 'loginSession']);

Route::middleware('auth:sanctum')->group(function () {

    // ===== DEBUG: ecoa request da PILHA API (normalmente Bearer / Sanctum) =====
    Route::match(['GET','POST','PUT','PATCH','DELETE'], '/_echo', function (Request $r) {
        $payload = [
            'stack'    => 'api',
            'method'   => $r->method(),
            'url'      => $r->fullUrl(),
            'ip'       => $r->ip(),
            'user'     => optional($r->user())->only(['id','nome','email']),
            'headers'  => $r->headers->all(),
            'query'    => $r->query(),
            'post'     => $r->post(),
        ];
        Log::info('API/_echo', $payload);
        return response()->json($payload);
    });

    Route::post('/auth/logout-token',   [AuthController::class, 'logoutToken']);
    Route::post('/auth/logout-session', [AuthController::class, 'logoutSession']);

    Route::apiResource('usuarios',    UsuarioController::class)->except(['create','edit']);
    Route::apiResource('categorias',  CategoriaController::class)->except(['create','edit']);
    Route::apiResource('tipos',       TipoController::class)->except(['create','edit']);
    Route::apiResource('prompts',     PromptController::class)->except(['create','edit']);
});
