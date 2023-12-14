<?php

namespace App\Servico;

use Illuminate\Http\Request;

interface IService
{

    function buscarPeloId($id);

    function cadastrar(Request $requisicao);
}