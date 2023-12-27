<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recebimentos_sat_bank extends Model
{
    use HasFactory;

    protected $fillable = [
        'movimentacao_cliente_comercios_id',
        'taxas_clientes',
        'taxas_comercios',
        'status',
    ];

    public function movimentacaoClienteComercio()
    {
        return $this->belongsTo(Movimentacao_cliente_comercio::class, 'movimentacao_cliente_comercios_id');
    }
}
