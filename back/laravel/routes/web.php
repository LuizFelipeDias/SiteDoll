<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use App\Http\Controllers\Auth\IndexController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;

use App\Http\Controllers\PromptController;
use App\Http\Controllers\CategoriaController;

/*
|----------------------------------------------------------------------
| Rotas ESPECÍFICAS primeiro (JSON)
|----------------------------------------------------------------------
*/

// Debug simples: devolve JSON (sem HTML)
Route::get('/_echo', function (Request $r) {
    return response()->json([
        'ok'       => true,
        'user_id'  => $r->user()?->id,
        'cookies'  => $r->cookies->all(),
        'accept'   => $r->header('Accept'),
        'xhr'      => $r->header('X-Requested-With'),
        'path'     => $r->path(),
    ]);
})->middleware('auth:web');

Route::get('/_debug-db', function () {
    return response()->json([
        'database'   => config('database.connections.mysql.database'),
        'host'       => config('database.connections.mysql.host'),
        'prompts'    => DB::table('prompts')->count(),
        'categorias' => DB::table('categorias')->count(),
        'tipos'      => DB::table('tipos')->count(),
    ]);
})->middleware('auth:web');

// Autenticação por sessão (guard web)
Route::post('/login',  [LoginController::class, 'login']);
Route::get('/login',   [IndexController::class, 'index'])->name('login');
Route::post('/logout', [LogoutController::class, 'perform']);

// Usuário autenticado (teste)
Route::get('/me', fn (Request $r) => $r->user())->middleware('auth:web');

// Leitura autenticada via sessão (SEM /api)
Route::middleware('auth:web')->group(function () {
    Route::get('/prompts',    [PromptController::class,   'index']);
    Route::get('/categorias', [CategoriaController::class,'index']);
});

/*
|----------------------------------------------------------------------
| SPA (por último): DUAS opções
|----------------------------------------------------------------------
*/

/* OPÇÃO A) Montar SPA SOMENTE em /app (recomendado, evita conflitos)
   Acesse o front por /app, e deixe /prompts /categorias como JSON.
*/
Route::get('/app/{any?}', [IndexController::class, 'index'])
    ->where('any', '.*');

// Página inicial simples (pode redirecionar para /app se quiser)
Route::get('/', [IndexController::class, 'index']);

/* OPÇÃO B) Se você quer catch-all geral, EXCLUA rotas críticas
Route::get('/{any}', [IndexController::class, 'index'])
    ->where('any', '^(?!api|prompts|categorias|me|login|logout|sanctum|_echo|_debug-db).*$');
*/
