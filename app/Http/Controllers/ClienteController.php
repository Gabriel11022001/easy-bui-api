<?php

namespace App\Http\Controllers;

use App\Servico\ClienteServico;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    private $clienteServico;

    public function __construct(ClienteServico $clienteServico) {
        $this->clienteServico = $clienteServico;
    }

    public function cadastrarCliente(Request $requisicao) {

        return $this->clienteServico->cadastrar($requisicao);
    }

    public function buscarTodosClientesUsuario($idUsuario) {

        return $this->clienteServico->buscarTodosClientesUsuario($idUsuario);
    }
}
