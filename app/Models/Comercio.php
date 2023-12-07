<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comercio extends Model
{
    use HasFactory;
    protected $fillable = [
        'razao_social', 'nome_fantasia', 'cnpj', 'inscricao_estadual', 'telefone', 'rua', 'numero', 'bairro', 'complemento', 'cidade', 'uf', 'prefeitura_id', 'users_id', 
    ]; 
    public function users()
    {
        return $this->belongsTo(User::class, 'users_id');
    }
    
}
