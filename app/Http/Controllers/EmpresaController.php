<?php

namespace App\Http\Controllers;

use App\Servico\EmpresaServico;
use Illuminate\Http\Request;

class EmpresaController extends Controller
{
    private $empresaServico;

    public function __construct(EmpresaServico $empresaServico) {
        $this->empresaServico = $empresaServico;
    }

    public function cadastrarEmpresa(Request $requisicao) {

        return $this->empresaServico->cadastrar($requisicao);
    }

    public function buscarEmpresaPeloCnpj(Request $requisicao) {

        return $this->empresaServico->buscarEmpresaPeloCnpj($requisicao);
    }
}
