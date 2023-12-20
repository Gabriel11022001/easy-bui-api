<?php

namespace App\Http\Controllers;

use App\Servico\LoginServico;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    private $loginServico;

    public function __construct(LoginServico $loginServico) {
        $this->loginServico = $loginServico;
    }

    public function login(Request $requisicao) {

        return $this->loginServico->login($requisicao);
    }

    public function logout(Request $requisicao) {

        return $this->loginServico->logout($requisicao);
    }

    public function obterUsuarioAutenticado(Request $requisicao) {

        return $this->loginServico->obterUsuarioAutenticado($requisicao);
    }
}
