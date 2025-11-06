<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\IndexController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;

// Página inicial (SPA / tela de login, etc)
Route::get('/', [IndexController::class, 'index']);

// Login e logout (guard web, sessão)

// Rota de login para convidados (usada pelo middleware Authenticate)
Route::get('/login', function () {
    // Ajuste o destino conforme sua SPA (ex.: hash router)
    return redirect('/app/#/login');
})->name('login');

Route::post('/logout', [LogoutController::class, 'perform']);

// Rota opcional para testar usuário autenticado via sessão
Route::get('/me', fn (\Illuminate\Http\Request $r) => $r->user())
    ->middleware('auth:web'); // ou só ->middleware('auth')

