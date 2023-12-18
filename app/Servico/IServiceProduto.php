<?php

namespace App\Servico;

use Illuminate\Http\Request;

interface IServiceProduto extends IService
{

    function editarProduto(Request $requisicao);

    function buscarTodosProdutosEmpresa($idEmpresa);

    function buscarProdutosAtivosEmpresa($idEmpresa);

    function alterarStatusProduto(Request $requisicao);

    function buscarProdutosPelaDescricao(Request $requisicao);

    function buscarProdutosPelaCategoria($idCategoria, $idEmpresa);

    function buscarProdutosEntrePrecos(Request $requisicao);

    function buscarProdutosZeradosEstoque($idEmpresa);

    function buscarProdutosAbaixoEstoqueMinimo($idEmpresa);
}