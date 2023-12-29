<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venda extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = [
        'id',
        'valor_total',
        'data_cadastro_venda',
        'data_limite_tratamento',
        'status',
        'usuario_id',
        'cliente_id',
        'forma_pagamento_id',
        'parcelado',
        'valor_ainda_falta_pagar',
        'valor_pago'
    ];
}
