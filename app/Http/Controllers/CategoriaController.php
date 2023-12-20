<?php

namespace App\Http\Controllers;

use App\Servico\CategoriaServico;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    private $categoriaServico;

    public function __construct(CategoriaServico $categoriaServico) {
        $this->categoriaServico = $categoriaServico;
    }

    public function cadastrarCategoria(Request $requisicao) {

        return $this->categoriaServico->cadastrar($requisicao);
    }

    public function buscarCategoriaPeloId($id) {

        return $this->categoriaServico->buscarPeloId($id);
    }

    public function buscarTodasCategoriasEmpresa($idEmpresa) {

        return $this->categoriaServico->buscarTodasCategoriasEmpresa($idEmpresa);
    }

    public function alterarStatusCategoria(Request $requisicao) {

        return $this->categoriaServico->alterarStatusCategoria($requisicao);
    }

    public function buscarCategoriasAtivas($idEmpresa) {

        return $this->categoriaServico->buscarCategoriasAtivas($idEmpresa);
    }
}
