<?php

namespace App\Http\Controllers;

use App\Servico\VendaServico;
use Illuminate\Http\Request;

class VendaController extends Controller
{
    private $vendaServico;

    public function __construct(VendaServico $vendaServico) {
        $this->vendaServico = $vendaServico;
    }

    public function realizarVenda(Request $requisicao) {

        return $this->vendaServico->cadastrar($requisicao);
    }

    public function buscarVendasUsuario($idUsuario) {

        return $this->vendaServico->buscarVendasUsuario($idUsuario);
    }

    public function buscarVendaPeloId($id) {

        return $this->vendaServico->buscarPeloId($id);
    }

    public function buscarVendasCliente($idUsuario, $idCliente) {

        return $this->vendaServico->buscarVendasCliente($idUsuario, $idCliente);
    }
}
