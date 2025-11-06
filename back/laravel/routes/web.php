<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\IndexController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;

// Página inicial (SPA / tela de login, etc)
Route::get('/', [IndexController::class, 'index']);

// Login e logout (guard web, sessão)
Route::post('/login',  [LoginController::class, 'login']);
<<<<<<< HEAD
Route::get('/login', [IndexController::class, 'index'])->name('login');
=======
Route::get('/login', [IndexController::class, 'index'])->name('login'); // << AQUI
>>>>>>> 479b437a945bea48db3995dafac77e966fcf7320

Route::post('/logout', [LogoutController::class, 'perform']);

// Rota opcional para testar usuário autenticado via sessão
Route::get('/me', fn (\Illuminate\Http\Request $r) => $r->user())
    ->middleware('auth:web'); // ou só ->middleware('auth')

