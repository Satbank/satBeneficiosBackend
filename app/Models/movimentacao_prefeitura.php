<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class movimentacao_prefeitura extends Model
{
    use HasFactory;
    protected $fillable = ['prefeituras_id', 'tipo', 'valor_alocado', 'saldo'];
    //para garantir que saldo é um valor decimal 
    protected $casts = [
        'saldo' => 'decimal:2', 
    ];
    public function prefeitura()
    {
        return $this->belongsTo(Prefeitura::class, 'prefeituras_id', 'id');
    }
}
