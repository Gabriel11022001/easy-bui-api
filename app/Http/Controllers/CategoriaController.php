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
}
