<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movimentacao_prefeitura_cliente extends Model
{
    use HasFactory;
    protected $fillable = [
        'users_id',
        'prefeituras_id',
        'tipo',
        'valor_movimentado_individual',
        'valor_movimentado',
        'descricao',
        'status'
        // Adicione outros campos permitidos aqui
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }
    public function cartao()
    {
        return $this->belongsTo(Cartao::class, 'clientes_id');
    }

    public function prefeitura()
    {
        return $this->belongsTo(Prefeitura::class, 'prefeituras_id');
    }

}
