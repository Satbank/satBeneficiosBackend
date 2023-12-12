<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cartao extends Model
{
    protected $table = 'cartoes';
    use HasFactory;

    protected $fillable = [
        'users_id',
        'tipo_cartao',
        'numero_cartao',
        'saldo_disponivel',
        'data_emissao',
        'status',
        'data_validade',
        'valor_alocado',
        'saldo_atual',
    ];

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'users_id', 'users_id');
    }
}
