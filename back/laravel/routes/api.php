<?php

// routes/api.php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\TipoController;
use App\Http\Controllers\PromptController;

Route::post('/cadastro', [UsuarioController::class, 'store']);

// Login por sessão (SPA Sanctum)
Route::post('/auth/login-session', [AuthController::class, 'loginSession']);
// (Opcional) login por token:
Route::post('/auth/login-token',   [AuthController::class, 'loginToken']);

Route::middleware('auth:sanctum')->group(function () {
    // usados pelo seu front:
    Route::get('/me', fn (Request $r) => $r->user());
    Route::post('/logout', [AuthController::class, 'logoutSession']);

    Route::apiResource('usuarios',   UsuarioController::class)->except(['create','edit']);
    Route::apiResource('categorias', CategoriaController::class)->except(['create','edit']);
    Route::apiResource('tipos',      TipoController::class)->except(['create','edit']);
    Route::apiResource('prompts',    PromptController::class)->except(['create','edit']);
});

// (opcional) leitura pública sem login:
// Route::apiResource('prompts',    PromptController::class)->only(['index','show']);
// Route::apiResource('categorias', CategoriaController::class)->only(['index','show']);
// Route::apiResource('tipos',      TipoController::class)->only(['index','show']);

