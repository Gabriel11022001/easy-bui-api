<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormaPagamento extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = [
        'id',
        'permite_parcelamento',
        'descricao',
        'status',
        'empresa_id'
    ];
}
