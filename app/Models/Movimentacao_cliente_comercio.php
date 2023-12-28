<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movimentacao_cliente_comercio extends Model
{
    use HasFactory;
    protected $fillable = [
        'cartoes_id',
        'comercios_id',
        'valor',
        'valor_original',
        'status',
    ];
    public function cartao()
    {
        return $this->belongsTo(Cartao::class, 'cartoes_id');
    }

    public function comercio()
    {
        return $this->belongsTo(Comercio::class, 'comercios_id');
    }
}
