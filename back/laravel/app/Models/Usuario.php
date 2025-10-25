<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Representa a tabela `usuarios`
 *
 * @property int         $id
 * @property string      $nome
 * @property string      $email
 * @property string      $cpf
 * @property string|null $telefone
 * @property string      $senha
 */
class Usuario extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /** Nome da tabela */
    protected $table = 'usuarios';

    /** Sua tabela não tem created_at/updated_at */
    public $timestamps = true;

    /** Campos fillable */
    protected $fillable = [
        'nome',
        'email',
        'cpf',
        'telefone',
        'senha',
    ];

    /** Ocultar senha nas respostas JSON */
    protected $hidden = [
        'senha',
    ];

    /** Hash automático da coluna 'senha' */
    protected $casts = [
        'senha' => 'hashed',
    ];

    /** Indica ao sistema de autenticação qual campo é a senha */
    public function getAuthPassword(): string
    {
        return $this->senha;
    }
}
