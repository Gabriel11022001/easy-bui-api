<?php

namespace App\Http\Controllers;

use App\Servico\ProdutoServico;
use Illuminate\Http\Request;

class ProdutoController extends Controller
{
    private $produtoServico;

    public function __construct(ProdutoServico $produtoServico) {
        $this->produtoServico = $produtoServico;
    }

    public function cadastrarProduto(Request $requisicao) {

        return $this->produtoServico->cadastrar($requisicao);
    }

    public function buscarTodosProdutosEmpresa($idEmpresa) {

        return $this->produtoServico->buscarTodosProdutosEmpresa($idEmpresa);
    }
}
