<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = [
        'id',
        'nome',
        'cpf',
        'rg',
        'email',
        'telefone',
        'sexo',
        'data_nascimento',
        'cep',
        'endereco',
        'bairro',
        'uf',
        'numero',
        'cidade',
        'usuario_id'
    ];
}
