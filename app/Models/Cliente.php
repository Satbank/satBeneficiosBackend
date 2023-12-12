<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;
    protected $fillable = [
        'nome', 'cpf', 'telefone', 'rua', 'numero', 'bairro', 'complemento', 'cidade','uf', 'prefeitura_id', 'perfil_id' 
    ];
    public function users()
    {
        return $this->belongsTo(User::class, 'users_id');
    }
   
}
