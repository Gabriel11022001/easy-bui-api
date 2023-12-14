<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = [
        'id',
        'cnpj',
        'razao_social',
        'data_abertura',
        'email',
        'url_site'
    ];

    public function usuarios() {

        return $this->hasMany(Usuario::class);
    }

    public function categorias() {

        return $this->hasMany(Categoria::class);
    }
}
