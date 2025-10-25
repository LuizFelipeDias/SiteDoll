<?php

namespace App\Http\Controllers;

use App\Models\Tipo;
use Illuminate\Http\Request;

class TipoController extends Controller
{
    public function index()
    {
        return Tipo::paginate(50);
    }

    public function show(Tipo $tipo)
    {
        return $tipo;
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nome' => 'required|string|max:120|unique:tipos,nome',
        ]);

        $tipo = Tipo::create($data);
        return response()->json($tipo, 201);
    }

    public function update(Request $request, Tipo $tipo)
    {
        $data = $request->validate([
            'nome' => "sometimes|string|max:120|unique:tipos,nome,{$tipo->id}",
        ]);

        $tipo->update($data);
        return response()->json($tipo);
    }

    public function destroy(Tipo $tipo)
    {
        $tipo->delete();
        return response()->json(['message' => 'Excluído']);
    }
}
