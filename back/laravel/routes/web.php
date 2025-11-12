<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use App\Http\Controllers\Auth\IndexController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\PromptController;
use App\Http\Controllers\CategoriaController;

/* -------- JSON / Sessão primeiro -------- */

Route::post('/login',  [LoginController::class, 'login']);
Route::get('/login',   [IndexController::class, 'index'])->name('login');
Route::post('/logout', [LogoutController::class, 'perform']);

Route::get('/me', fn (Request $r) => $r->user())->middleware('auth:web');

Route::get('/_echo', function (Request $r) {
    return response()->json([
        'ok'      => true,
        'user_id' => $r->user()?->id,
        'path'    => $r->path(),
        'accept'  => $r->header('Accept'),
    ]);
})->middleware('auth:web');

Route::get('/_debug-db', function () {
    return response()->json([
        'database'   => config('database.connections.mysql.database'),
        'host'       => config('database.connections.mysql.host'),
        'prompts'    => DB::table('prompts')->count(),
        'categorias' => DB::table('categororias')->count(),
        'tipos'      => DB::table('tipos')->count(),
    ]);
})->middleware('auth:web');

Route::middleware('auth:web')->group(function () {
    Route::get('/prompts',    [PromptController::class,   'index']);
    Route::get('/categorias', [CategoriaController::class,'index']);
});

/* -------- SPA por último (escape das rotas JSON) -------- */

// Opção B (SPA em / também), mas EXCLUINDO caminhos de API/JSON:
Route::get('/{any}', [IndexController::class, 'index'])
    ->where('any', '^(?!api|prompts|categorias|me|login|logout|sanctum|_echo|_debug-db).*$');

// (Se preferir, use a Opção A e sirva o SPA só em /app/{any}, e deixe "/" simples.)
