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

    public function buscarProdutoPeloId($id) {

        return $this->produtoServico->buscarPeloId($id);
    }

    public function buscarProdutosAbaixoEstoqueMinimo($idEmpresa) {

        return $this->produtoServico->buscarProdutosAbaixoEstoqueMinimo($idEmpresa);
    }

    public function buscarProdutosPelaCategoria($idCategoria, $idEmpresa) {

        return $this->produtoServico->buscarProdutosPelaCategoria($idCategoria, $idEmpresa);
    }

    public function buscarProdutosPelaDescricao(Request $requisicao) {

        return $this->produtoServico->buscarProdutosPelaDescricao($requisicao);
    }
}
