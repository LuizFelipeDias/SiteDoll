<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    public function index(Request $request)
    {
        $perPage = max(1, $request->integer('per_page', 10)); // sem teto

        return Categoria::query()
            ->select(['id','nome','parent_id'])
            ->with(['parent:id,nome'])
            ->withCount('children')
            ->latest('id')
            ->paginate($perPage);
    }

    public function show(Categoria $categoria)
    {
        return $categoria;
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nome' => 'required|string|max:120|unique:categorias,nome',
            'parent_id' => 'nullable|exists:categorias,id',
        ]);

        $categoria = Categoria::create($data);
        return response()->json($categoria, 201);
    }

    public function update(Request $request, Categoria $categoria)
    {
        $data = $request->validate([
            'nome' => "sometimes|string|max:120|unique:categorias,nome,{$categoria->id}",
            'parent_id' => 'nullable|exists:categorias,id',
        ]);

        $categoria->update($data);
        return response()->json($categoria);
    }

    public function destroy(Categoria $categoria)
    {
        $categoria->delete();
        return response()->json(['message' => 'Excluída']);
    }
}
