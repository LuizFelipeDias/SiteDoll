<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UsuarioController extends Controller
{
    public function index(): LengthAwarePaginator
    {
        return Usuario::select('id', 'nome', 'email', 'telefone')->paginate(20);
    }

    public function show(Usuario $usuario): JsonResponse
    {
        return response()->json([
            'id'       => $usuario->id,
            'nome'     => $usuario->nome,
            'email'    => $usuario->email,
            'telefone' => $usuario->telefone,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        // NÃO desmascarar CPF; a tabela tem CHECK para formato 000.000.000-00
        $data = $request->validate([
            'nome'     => ['required','string','max:255'],
            'email'    => ['required','email','max:255','unique:usuarios,email'],
            'cpf'      => ['required','regex:/^\d{3}\.\d{3}\.\d{3}-\d{2}$/','unique:usuarios,cpf'],
            'telefone' => ['nullable','string','max:30'], // telefone pode vir com máscara
            'password' => ['required','string','min:6'],
        ]);

        $usuario = Usuario::create([
            'nome'     => $data['nome'],
            'email'    => $data['email'],
            'cpf'      => $data['cpf'],               // já mascarado
            'telefone' => $data['telefone'] ?? null,  // mascarado ou não, sem CHECK
            'senha'    => $data['password'],          // Model deve ter cast 'hashed' para 'senha'
        ]);

        return response()->json([
            'message' => 'Usuário criado com sucesso',
            'id'      => $usuario->id,
        ], 201);
    }

    public function update(Request $request, Usuario $usuario): JsonResponse
    {
        // manter coerência: CPF **mascarado** também no update
        $data = $request->validate([
            'nome'     => ['sometimes','string','max:255'],
            'email'    => ['sometimes','email','max:255', Rule::unique('usuarios','email')->ignore($usuario->id)],
            'cpf'      => ['sometimes','regex:/^\d{3}\.\d{3}\.\d{3}-\d{2}$/', Rule::unique('usuarios','cpf')->ignore($usuario->id)],
            'telefone' => ['nullable','string','max:30'],
            'password' => ['nullable','string','min:6'],
        ]);

        $usuario->fill(collect($data)->except('password')->toArray());

        if (!empty($data['password'])) {
            // se o Model NÃO tiver cast 'hashed', mantenha o bcrypt:
            $usuario->senha = bcrypt($data['password']);
        }

        $usuario->save();

        return response()->json(['message' => 'Usuário atualizado com sucesso']);
    }

    public function destroy(Usuario $usuario): JsonResponse
    {
        $usuario->delete();
        return response()->json([], 204);
    }
}
