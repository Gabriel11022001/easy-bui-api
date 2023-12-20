<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Authenticatable
{
    use HasFactory, HasApiTokens, Notifiable;

    public $timestamps = false;
    protected $fillable = [
        'id',
        'nome',
        'telefone',
        'email',
        'senha',
        'senha_confirmacao',
        'data_nascimento',
        'sexo',
        'cpf',
        'empresa_id',
        'cpf',
        'cep',
        'endereco',
        'bairro',
        'cidade',
        'uf',
        'numero'
    ];
}
