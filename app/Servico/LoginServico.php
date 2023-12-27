<?php

namespace App\Servico;

use App\Models\Usuario;
use App\Utils\Resposta;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class LoginServico
{

    public function login(Request $requisicao) {

        try {
            $validador = Validator::make($requisicao->all(),
            [
                'email' => 'required|email',
                'senha' => 'required|min:8|max:25'
            ],
            [
                'email.required' => 'Informe o e-mail!',
                'email.email' => 'E-mail inválido!',
                'senha.required' => 'Informe a senha!',
                'senha.min' => 'A senha deve possuir no mínimo 8 caracteres!',
                'senha.max' => 'A senha deve possuir no máximo 25 caracteres!'
            ]);

            if ($validador->fails()) {
                Log::warning('Ocorreram erros de validação de dados!', $validador->errors()->toArray());

                return Resposta::resposta(
                    'Ocorreram erros de validação de dados!',
                    $validador->errors(),
                    200,
                    false
                );
            }

            $usuario = $this->buscarUsuarioPeloEmailESenha(
                $requisicao->email,
                md5($requisicao->senha)
            );

            if (!$usuario) {
                Log::alert('O usuário informou um e-mail ou senha inválido!', [
                    'email' => $requisicao->email
                ]);

                return Resposta::resposta(
                    'E-mail ou senha inválidos!',
                    null,
                    200,
                    false
                );
            }

            /**
             * todos os tokens do usuário serão deletados
             * e o mesmo será deslogado em todos os outros dispositivos
             */
            $this->deletarTodosTokensUsuario($usuario);
            $token = $this->gerarToken($usuario);
            Log::debug('O usuário realizou o login com sucesso!', [
                'email' => $requisicao->email,
                'token' => $token
            ]);

            return Resposta::resposta(
                'Login efetuado com sucesso!',
                [
                    'token' => $token
                ],
                200,
                true
            );
        } catch (Exception $e) {
            Log::error('Ocorreu o seguinte erro ao tentar-se realizar o login: ' . $e->getMessage());

            return Resposta::resposta(
                'Ocorreu um erro ao tentar-se realizar login!' . $e->getMessage(),
                null,
                200,
                false
            );
        }

    }

    private function buscarUsuarioPeloEmailESenha($email, $senha) {
        
        return Usuario::where('email', $email)
            ->where('senha', $senha)
            ->first();
    }

    private function gerarToken($usuario) {

        return $usuario->createToken($usuario->email)->plainTextToken;
    }

    private function deletarTodosTokensUsuario($usuario) {
        $usuario->tokens()->delete();
    }

    public function logout(Request $requisicao) {
        $usuarioAutenticado = $requisicao->user();

        if (!$usuarioAutenticado) {

            return Resposta::resposta(
                'Não existe um usuário autenticado!',
                null,
                200,
                false
            );
        }

        $usuarioAutenticado->tokens()->delete();
        Log::debug(
            'O usuário realizou o logout com sucesso!',
            [
                'usuario' => $usuarioAutenticado->nome,
                'email' => $usuarioAutenticado->email
            ]
        );

        return Resposta::resposta(
            'Logout efetuado com sucesso!',
            null,
            200,
            true
        );
    }

    public function obterUsuarioAutenticado(Request $requisicao) {
        $usuario = $requisicao->user();

        if (!$usuario) {

            return Resposta::resposta(
                'Não existe um usuário autenticado!',
                null,
                200,
                false
            );
        }

        return Resposta::resposta(
            'Usuário autenticado obtido com sucesso!',
            $usuario,
            200,
            true
        );
    }
}