<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'usuarios';

    // << se NÃO tem created_at/updated_at, deixe false
    public $timestamps = false;

    protected $fillable = ['nome','email','cpf','telefone','senha'];

    protected $hidden = ['senha', 'remember_token'];

    // Cast automático (NÃO use bcrypt em controllers)
    protected $casts = [
        'senha' => 'hashed',
    ];

    public function getAuthPassword(): string
    {
        return $this->senha;
    }
}
