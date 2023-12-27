<?php

namespace App\Servico;

use Illuminate\Http\Request;

interface IServicoCliente extends IService
{
    
    function editarCliente(Request $requisicao);

    function buscarTodosClientesUsuario($idUsuario);

    function buscarClientesAtivosUsuario($idUsuario);

    function buscarClientesPeloCpf($cpf, $idUsuario);

    function buscarClientesPeloRg($rg, $idUsuario);

    function buscarClientesPeloEmail($email, $idUsuario);

    function buscarClientesPeloNome($nome, $idUsuario);
}