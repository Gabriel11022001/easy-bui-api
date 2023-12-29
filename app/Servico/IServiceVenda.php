<?php

namespace App\Servico;

use Illuminate\Http\Request;

interface IServiceVenda extends IService
{

    function pagar(Request $requisicao);

    function buscarVendasUsuario($idUsuario);

    function buscarVendasCliente($idCliente, $idUsuario);

    function buscarVendasPeloStatus($status, $idUsuario);
}