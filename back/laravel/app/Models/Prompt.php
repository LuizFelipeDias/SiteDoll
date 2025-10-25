<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prompt extends Model
{
    protected $table = 'prompts';

    protected $fillable = [
        'titulo', 'descricao', 'corpo',
        'tipo_id', 'categoria_id',
        'created_by', 'updated_by',
    ];

    public function tipo()
    {
        return $this->belongsTo(Tipo::class, 'tipo_id');
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    public function autor()
    {
        return $this->belongsTo(Usuario::class, 'created_by');
    }

    public function editor()
    {
        return $this->belongsTo(Usuario::class, 'updated_by');
    }
}
