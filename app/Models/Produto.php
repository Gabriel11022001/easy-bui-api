<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produto extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = [
        'id',
        'descricao',
        'preco',
        'status',
        'foto_produto',
        'qtd_unidades_estoque',
        'estoque_minimo',
        'estoque_maximo',
        'percentual_desconto',
        'desconto_dinheiro',
        'categoria_id',
        'empresa_id'
    ];

    public function categoria() {

        return $this->hasOne(Categoria::class);
    }

    public function empresa() {

        return $this->hasOne(Empresa::class);
    }
}
