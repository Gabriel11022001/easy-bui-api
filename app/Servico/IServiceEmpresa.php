<?php

namespace App\Servico;

use Illuminate\Http\Request;

interface IServiceEmpresa extends IService
{

    function editarEmpresa(Request $requisicao);

    function buscarEmpresaPeloCnpj(Request $requisicao);
}