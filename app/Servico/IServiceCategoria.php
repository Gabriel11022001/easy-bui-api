<?php

namespace App\Servico;

use Illuminate\Http\Request;

interface IServiceCategoria extends IService
{

    function buscarTodasCategoriasEmpresa($idEmpresa);

    function alterarStatusCategoria(Request $requisicao);

    function editarCategoria(Request $requisicao);

    function buscarCategoriasAtivas($idEmpresa);
}