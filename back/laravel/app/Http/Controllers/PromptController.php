<?php

namespace App\Http\Controllers;

use App\Models\Prompt;
use Illuminate\Http\Request;

class PromptController extends Controller
{
    public function index(Request $request)
    {
        // pega per_page da query (ex.: ?per_page=15); default 20
        $perPage = $request->integer('per_page', 10);

        // garante no mínimo 1 por página (sem teto máximo)
        $perPage = max(1, $perPage);

        return Prompt::with(['tipo','categoria','autor:id,nome','editor:id,nome'])
            ->latest('id')
            ->paginate($perPage);
    }


    public function show(Prompt $prompt)
    {
        return $prompt->load(['tipo','categoria','autor:id,nome','editor:id,nome']);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo'       => 'required|string|max:255',
            'descricao'    => 'required|string',
            'corpo'        => 'required|string',
            'tipo_id'      => 'required|exists:tipos,id',
            'categoria_id' => 'required|exists:categorias,id',
        ]);

        // Sem auth por enquanto — manterá null (depois, com Sanctum, preenche com $request->user()->id)
        $data['created_by'] = null;
        $data['updated_by'] = null;

        $p = Prompt::create($data);

        return response()->json($p->load(['tipo','categoria']), 201);
    }

    public function update(Request $request, Prompt $prompt)
    {
        $data = $request->validate([
            'titulo'       => 'sometimes|string|max:255',
            'descricao'    => 'sometimes|string',
            'corpo'        => 'sometimes|string',
            'tipo_id'      => 'sometimes|exists:tipos,id',
            'categoria_id' => 'sometimes|exists:categorias,id',
        ]);

        $data['updated_by'] = null; // depois trocamos para $request->user()->id com auth

        $prompt->update($data);

        return response()->json($prompt->load(['tipo','categoria']));
    }

    public function destroy(Prompt $prompt)
    {
        $prompt->delete();
        return response()->json(['message' => 'Excluído']);
    }
}
