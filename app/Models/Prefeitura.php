<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prefeitura extends Model
{
    use HasFactory;
    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }
    public function movimentacoes()
    {
        return $this->hasMany(Movimentacao_prefeitura_cliente::class, 'prefeituras_id');
    }
}
