<?php

namespace App\Http\Controllers;

use App\Models\Prompt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PromptController extends Controller
{
    public function index(Request $request)
    {
        // LOG de entrada
        Log::info('PromptController@index:IN', [
            'user'    => optional($request->user())->only(['id','nome','email']),
            'session' => method_exists($request, 'session') ? $request->session()->getId() : null,
            'query'   => $request->query(),
            'headers' => $request->headers->all(),
            'cookies' => $request->cookies->all(),
        ]);

        // paginação
        $perPage = max(1, $request->integer('per_page', 10));

        $paginated = Prompt::with(['tipo','categoria','autor:id,nome','editor:id,nome'])
            ->latest('id')
            ->paginate($perPage);

        // Se ?debug=1, acrescenta bloco "debug" na resposta
        if ($request->boolean('debug')) {
            $debug = [
                'count_page' => $paginated->count(),
                'total'      => $paginated->total(),
                'current'    => $paginated->currentPage(),
                'per_page'   => $paginated->perPage(),
                'has_user'   => (bool) $request->user(),
                'session_id' => method_exists($request, 'session') ? $request->session()->getId() : null,
            ];

            $out = $paginated->toArray();
            $out['debug'] = $debug;

            Log::info('PromptController@index:OUT(DEBUG)', [
                'meta'  => $paginated->toArray()['meta'] ?? null,
                'debug' => $debug,
            ]);

            return response()
                ->json($out)
                ->header('X-Debug-Tag', 'prompts-index');
        }

        Log::info('PromptController@index:OUT', [
            'meta' => $paginated->toArray()['meta'] ?? null,
        ]);

        return response()->json($paginated);
    }

    public function show(Prompt $prompt, Request $request)
    {
        Log::info('PromptController@show:IN', [
            'id'     => $prompt->id,
            'user'   => optional($request->user())->only(['id','nome','email']),
            'query'  => $request->query(),
        ]);

        $loaded = $prompt->load(['tipo','categoria','autor:id,nome','editor:id,nome']);

        Log::info('PromptController@show:OUT', ['id' => $prompt->id]);

        return response()->json($loaded);
    }

    public function store(Request $request)
    {
        Log::info('PromptController@store:IN', [
            'user'  => optional($request->user())->only(['id','nome','email']),
            'post'  => $request->post(),
        ]);

        $data = $request->validate([
            'titulo'       => 'required|string|max:255',
            'descricao'    => 'required|string',
            'corpo'        => 'required|string',
            'tipo_id'      => 'required|exists:tipos,id',
            'categoria_id' => 'required|exists:categorias,id',
        ]);

        $data['created_by'] = optional($request->user())->id; // null se anônimo
        $data['updated_by'] = optional($request->user())->id;

        $p = Prompt::create($data);

        Log::info('PromptController@store:OUT', ['id' => $p->id]);

        return response()->json($p->load(['tipo','categoria']), 201);
    }

    public function update(Request $request, Prompt $prompt)
    {
        Log::info('PromptController@update:IN', [
            'id'    => $prompt->id,
            'user'  => optional($request->user())->only(['id','nome','email']),
            'post'  => $request->post(),
        ]);

        $data = $request->validate([
            'titulo'       => 'sometimes|string|max:255',
            'descricao'    => 'sometimes|string',
            'corpo'        => 'sometimes|string',
            'tipo_id'      => 'sometimes|exists:tipos,id',
            'categoria_id' => 'sometimes|exists:categorias,id',
        ]);

        $data['updated_by'] = optional($request->user())->id;

        $prompt->update($data);

        Log::info('PromptController@update:OUT', ['id' => $prompt->id]);

        return response()->json($prompt->load(['tipo','categoria']));
    }

    public function destroy(Prompt $prompt, Request $request)
    {
        Log::info('PromptController@destroy:IN', [
            'id'   => $prompt->id,
            'user' => optional($request->user())->only(['id','nome','email']),
        ]);

        $prompt->delete();

        Log::info('PromptController@destroy:OUT', ['id' => $prompt->id]);

        return response()->json(['message' => 'Excluído']);
    }
}
