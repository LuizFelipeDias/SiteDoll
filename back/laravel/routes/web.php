<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

use App\Http\Controllers\Auth\IndexController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;

// Controladores de domínio
use App\Http\Controllers\PromptController;
use App\Http\Controllers\CategoriaController;

// Página inicial (SPA / tela de login, etc)
Route::get('/', [IndexController::class, 'index']);

// Login e logout (guard web, sessão)
Route::post('/login',  [LoginController::class, 'login']);
Route::get('/login', [IndexController::class, 'index'])->name('login');
Route::post('/logout', [LogoutController::class, 'perform']);

// ===== DEBUG: ecoa request da PILHA WEB (com sessão) =====
Route::match(['GET','POST','PUT','PATCH','DELETE'], '/_echo', function (Request $r) {
    $payload = [
        'stack'    => 'web',
        'method'   => $r->method(),
        'url'      => $r->fullUrl(),
        'ip'       => $r->ip(),
        'user'     => optional($r->user())->only(['id','nome','email']),
        'session'  => [
            'id'      => $r->session()->getId(),
            'data'    => $r->session()->all(),
        ],
        'cookies'  => $r->cookies->all(),
        'headers'  => $r->headers->all(),
        'query'    => $r->query(),
        'post'     => $r->post(),
    ];
    Log::info('WEB/_echo', $payload);
    return response()->json($payload);
})->middleware('auth:web');

Route::get('/_debug-db', function () {
    $out = [
        'database'   => config('database.connections.mysql.database'),
        'host'       => config('database.connections.mysql.host'),
        'prompts'    => DB::table('prompts')->count(),
        'categorias' => DB::table('categororias')->count() ?? DB::table('categorias')->count(), // tolerância nome
        'tipos'      => DB::table('tipos')->count(),
    ];
    Log::info('WEB/_debug-db', $out);
    return response()->json($out);
})->middleware('auth:web');

// Usuário autenticado (teste)
Route::get('/me', fn (Request $r) => $r->user())->middleware('auth:web');

// Leitura via sessão (sem /api)
Route::middleware('auth:web')->group(function () {
    Route::get('/prompts',    [PromptController::class,   'index']);
    Route::get('/categorias', [CategoriaController::class,'index']);
});
