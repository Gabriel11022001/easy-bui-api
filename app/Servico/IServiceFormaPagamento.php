<?php

namespace App\Servico;

use Illuminate\Http\Request;

interface IServiceFormaPagamento extends IService
{

    function alterarStatusFormaPagamento($idFormaPagamento, $idEmpresa);

    function buscarTodasFormasPagamentoEmpresa($idEmpresa);

    function buscarTodasFormasPagamentoEmpresaAtivas($idEmpresa);

    function editarFormaPagamento(Request $requisicao);
}