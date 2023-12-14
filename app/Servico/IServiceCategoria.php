<?php

namespace App\Servico;

use Illuminate\Http\Request;

interface IServiceCategoria extends IService
{

    function buscarTodasCategoriasEmpresa($idEmpresa);

    function alterarStatusCategoria($idCategoria);

    function editarCategoria(Request $requisicao);
}