<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\AuthController;

use Illuminate\Http\Request;


Route::post('/cadastro', [UsuarioController::class, 'store']);


Route::post('/auth/login-token',   [AuthController::class, 'loginToken']);
Route::post('/auth/login-session', [AuthController::class, 'loginSession']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout-token',   [AuthController::class, 'logoutToken']);
    Route::post('/auth/logout-session', [AuthController::class, 'logoutSession']);

    Route::apiResource('usuarios', \App\Http\Controllers\UsuarioController::class)->except(['create','edit']);
    Route::apiResource('categorias', \App\Http\Controllers\CategoriaController::class)->except(['create','edit']);
    Route::apiResource('tipos', \App\Http\Controllers\TipoController::class)->except(['create','edit']);
    Route::apiResource('prompts', \App\Http\Controllers\PromptController::class)->except(['create','edit']);
});
