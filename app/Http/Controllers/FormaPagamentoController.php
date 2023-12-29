<?php

namespace App\Http\Controllers;

use App\Servico\FormaPagamentoServico;
use Illuminate\Http\Request;

class FormaPagamentoController extends Controller
{
    private $formaPagamentoServico;

    public function __construct(FormaPagamentoServico $formaPagamentoServico) {
        $this->formaPagamentoServico = $formaPagamentoServico;
    }

    public function cadastrarFormaPagamento(Request $requisicao) {

        return $this->formaPagamentoServico->cadastrar($requisicao);
    }

    public function buscarTodasFormasPagamentoEmpresa($idEmpresa) {

        return $this->formaPagamentoServico->buscarTodasFormasPagamentoEmpresa($idEmpresa);
    }
}
