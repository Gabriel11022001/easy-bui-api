<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parcela extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = [
        'id',
        'numero_parcela',
        'valor',
        'data_limite_pagamento',
        'pago',
        'venda_id'
    ];
}
