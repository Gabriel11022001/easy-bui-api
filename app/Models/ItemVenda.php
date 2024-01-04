<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemVenda extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = [
        'id',
        'preco_produto',
        'qtd_unidades',
        'produto_id',
        'venda_id'
    ];
}
