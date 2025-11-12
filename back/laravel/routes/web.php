<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\IndexController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;

// importa os controladores de domínio
use App\Http\Controllers\PromptController;
use App\Http\Controllers\CategoriaController;

// Página inicial (SPA / tela de login, etc)
Route::get('/', [IndexController::class, 'index']);

// Login e logout (guard web, sessão)
Route::post('/login',  [LoginController::class, 'login']);
Route::get('/login', [IndexController::class, 'index'])->name('login');
Route::post('/logout', [LogoutController::class, 'perform']);

// Usuário autenticado (teste)
Route::get('/me', fn (Request $r) => $r->user())->middleware('auth:web');

// ADICIONE: leitura via sessão (sem /api)
Route::middleware('auth:web')->group(function () {
    Route::get('/prompts',    [PromptController::class,   'index']);
    Route::get('/categorias', [CategoriaController::class,'index']);
});
