<?php

namespace App\Servico;

use Illuminate\Http\Request;

class VendaServico implements IServiceVenda
{

    public function cadastrar(Request $requisicao) {
        
    }

    public function buscarPeloId($id) {
        
    }

    public function buscarVendasCliente($idCliente, $idUsuario) {
        
    }

    public function buscarVendasUsuario($idUsuario) {
        
    }

    public function buscarVendasPeloStatus($status, $idUsuario) {
        
    }

    public function pagar(Request $requisicao) {
        
    }
}