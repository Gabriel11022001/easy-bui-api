<?php

namespace App\Servico;

use App\Models\Cliente;
use App\Models\Usuario;
use App\Utils\Resposta;
use App\Utils\ValidaNumeroResidenciaUtils;
use App\Utils\ValidaSexoUtils;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ClienteServico implements IServicoCliente
{

    private $validadoresCadastroCliente = [
        'nome' => 'required|min:3|max:255',
        'email' => 'nullable|email|max:255',
        'cpf' => 'required|cpf',
        'data_nascimento' => 'required|date_format:Y-m-d',
        'sexo' => 'required|max:255',
        'telefone' => 'nullable|max:255|celular_com_ddd',
        'rg' => 'required|max:255',
        'cep' => 'required|formato_cep',
        'endereco' => 'required',
        'bairro' => 'required|max:255',
        'cidade' => 'required|max:255',
        'uf' => 'required|uf',
        'usuario_id' => 'required|numeric|min:1'
    ];
    private $mensagensValidadoresCadastroCliente = [
        'nome' => [
            'required' => 'Informe o nome do cliente!',
            'min' => 'O nome deve possuir no mínimo 3 caracteres!',
            'max' => 'O nome deve possuir no máximo 255 caracteres!'
        ],
        'email' => [
            'email' => 'Informe um e-mail válido!',
            'max' => 'O e-mail deve possuir no máximo 255 caracteres!'
        ],
        'cpf' => [
            'required' => 'Informe o cpf do cliente!',
            'cpf' => 'Informe um cpf válido!'
        ],
        'data_nascimento' => [
            'required' => 'Informe a data de nascimento do cliente!',
            'date_format' => 'A data de nascimento do cliente deve estar no formato Y-m-d!'
        ],
        'sexo' => [
            'required' => 'Informe o sexo do cliente!',
            'max' => 'O sexo do cliente deve possuir no máximo 255 caracteres!'            
        ],
        'telefone' => [
            'max' => 'O telefone deve possuir no máximo 255 caracteres!',
            'celular_com_ddd' => 'Informe um telefone no formato válido!'
        ],
        'rg' => [
            'required' => 'Informe o rg do cliente!',
            'max' => 'O rg do cliente não deve possuir mais de 255 caracteres!'
        ],
        'cep' => [
            'required' => 'Informe o cep!',
            'formato_cep' => 'Informe um cep válido!'
        ],
        'endereco' => [
            'required' => 'Informe o endereço!'
        ],
        'bairro' => [
            'required' => 'Informe o bairro!',
            'max' => 'O bairro deve possuir no máximo 255 caracteres!'
        ],
        'cidade' => [
            'required' => 'Informe a cidade!',
            'max' => 'A cidade deve possuir no máximo 255 caracteres!'
        ],
        'uf' => [
            'uf' => 'A uf informada é inválida!',
            'required' => 'Informe a uf!'
        ],
        'usuario_id' => [
            'required' => 'Informe o id do usuário que será o vendedor para esse cliente!',
            'numeric' => 'O id do usuário deve ser um valor numérico!',
            'min' => 'O id do usuário deve ser um valor maior ou igual a 1!'
        ]
    ];

    public function cadastrar(Request $requisicao) {
        
        try {
            $validador = Validator::make(
                $requisicao->all(),
                $this->validadoresCadastroCliente,
                $this->mensagensValidadoresCadastroCliente
            );

            if ($validador->fails()) {
                Log::warning(
                    'Ocorreram erros de validação de dados!',
                    $validador->errors()->toArray()
                );
                
                return Resposta::resposta(
                    'Ocorreram erros de validação de dados!',
                    $validador->errors(),
                    200,
                    false
                );
            }

            $errosPersonalizadosCampos = [];

            if (!ValidaSexoUtils::validarSexo($requisicao->sexo)) {
                $errosPersonalizadosCampos['sexo'] = 'O sexo informado é inválido!';
            }

            if (mb_strlen(ValidaNumeroResidenciaUtils::validarNumeroResidencia($requisicao->numero)) > 0) {
                $errosPersonalizadosCampos['numero'] = 'Número de residência inválido!';
            }

            if (count($errosPersonalizadosCampos) > 0) {
                Log::warning(
                    'Ocorreram erros de validação de dados!',
                    $errosPersonalizadosCampos
                );
                
                return Resposta::resposta(
                    'Ocorreram erros de validação de dados!',
                    $errosPersonalizadosCampos,
                    200,
                    false
                );
            }

            $usuario = Usuario::find($requisicao->usuario_id);

            if (!$usuario) {

                return Resposta::resposta(
                    'Não existe um usuário cadastrado no banco de dados com esse id!',
                    null,
                    200,
                    false
                );
            }

            $clienteCadastradoComEmailInformado = Cliente::where('email', $requisicao->email)
                ->where('usuario_id', $requisicao->usuario_id)
                ->get();
            
            if (count($clienteCadastradoComEmailInformado) > 0) {

                return Resposta::resposta(
                    'Você já possui um cliente cadastrado com esse e-mail!',
                    null,
                    200,
                    false
                );
            }    

            $clienteCadastradoComCpfInformado = Cliente::where('cpf', $requisicao->cpf)
                ->where('usuario_id', $requisicao->usuario_id)
                ->get();
            
            if (count($clienteCadastradoComCpfInformado) > 0) {

                return Resposta::resposta(
                    'Você já possui um cliente cadastrado com esse cpf!',
                    null,
                    200,
                    false
                );
            }
            
            $clienteCadastradoComRgInformado = Cliente::where('rg', $requisicao->rg)
                ->where('usuario_id', $requisicao->usuario_id)
                ->get();

            if (count($clienteCadastradoComRgInformado) > 0) {

                return Resposta::resposta(
                    'Você já possui um cliente cadastrado com o rg informado!',
                    null,
                    200,
                    false
                );
            }

            $clienteCadastradoComTelefoneInformado = Cliente::where('telefone', $requisicao->telefone)
                ->where('usuario_id', $requisicao->usuario_id)
                ->get();

            if (count($clienteCadastradoComTelefoneInformado) > 0) {

                return Resposta::resposta(
                    'Você já possui um cliente cadastrado com o telefone informado!',
                    null,
                    200,
                    false
                );
            }

            $cliente = new Cliente();
            $cliente->nome = mb_strtoupper($requisicao->nome);
            $cliente->telefone = $requisicao->telefone;
            $cliente->sexo = $requisicao->sexo;
            $cliente->email = $requisicao->email;
            $cliente->data_nascimento = $requisicao->data_nascimento;
            $cliente->cpf = $requisicao->cpf;
            $cliente->rg = $requisicao->rg;
            $cliente->cep = $requisicao->cep;
            $cliente->endereco = $requisicao->endereco;
            $cliente->bairro = $requisicao->bairro;
            $cliente->cidade = $requisicao->cidade;
            $cliente->numero = $requisicao->numero;
            $cliente->uf = $requisicao->uf;
            $cliente->numero = empty($requisicao->numero) ? 'S/N' : $requisicao->numero;
            $cliente->usuario_id = $requisicao->usuario_id;

            if (!$cliente->save()) {
                Log::error(
                    'Ocorreu um erro ao tentar-se cadastrar o cliente! usuario_id: ' . auth()->user()->id,
                    $requisicao->all()
                );

                return Resposta::resposta(
                    'Ocorreu um erro ao tentar-se cadastrar o cliente!',
                    null,
                    200,
                    false
                );
            }

            Log::debug(
                'Cliente cadastrado com sucesso! usuario_id: ' . auth()->user()->id,
                $cliente->toArray()
            );

            return Resposta::resposta(
                'Cliente cadastrado com sucesso!',
                $cliente,
                201,
                true
            );
        } catch (Exception $e) {
            Log::error(
                'Ocorreu o seguinte erro ao tentar-se cadastrar o cliente: ' . $e->getMessage() . ' usuario_id: ' . auth()->user()->id
            );

            return Resposta::resposta(
                'Ocorreu um erro ao tentar-se cadastrar o cliente!' . $e->getMessage(),
                null,
                200,
                false
            );
        }

    }

    public function buscarPeloId($id) {
        
    }

    public function editarCliente(Request $requisicao) {
        
    }

    public function buscarClientesAtivosUsuario($idUsuario) {
        
    }

    public function buscarClientesPeloCpf($cpf, $idUsuario) {
        
        try {
            $validador = Validator::make([
                'usuario_id' => $idUsuario,
                'cpf' => $cpf
            ],
            [
                'usuario_id' => 'required|numeric|min:1',
                'cpf' => 'required|cpf'
            ],
            [
                'usuario_id.required' => 'Informe o id do usuário!',
                'usuario_id.numeric' => 'O id do usuário deve ser um valor numérico!',
                'usuario_id.min' => 'O id do usuário deve ser um valor maior ou igual a 1!',
                'cpf.required' => 'Informe o cpf do cliente!',
                'cpf.cpf' => 'Cpf inválido!'
            ]);

            if ($validador->fails()) {
                Log::warning(
                    'Ocorreram erros de validação de dados! usuario_id: ' . auth()->user()->id,
                    $validador->errors()->toArray()
                );

                return Resposta::resposta(
                    'Ocorreram erros de validação de dados!',
                    $validador->errors(),
                    200,
                    false
                );
            }

            $usuario = Usuario::find($idUsuario);

            if (!$usuario) {

                return Resposta::resposta(
                    'Não existe um usuário cadastrado com esse id!',
                    null,
                    200,
                    false
                );
            }

            $cliente = Cliente::where('cpf', $cpf)
                ->where('usuario_id', $idUsuario)
                ->get()
                ->first();
            
            if (!$cliente) {

                return Resposta::resposta(
                    'Você não possui um cliente com esse cpf!',
                    null,
                    200,
                    false
                );
            }
            
            return Resposta::resposta(
                'Cliente encontrado com sucesso!',
                $cliente,
                200,
                true
            );
        } catch (Exception $e) {
            Log::error(
                'Ocorreu o seguinte erro ao tentar-se buscar os clientes pelo cpf: ' . $e->getMessage() . ' usuario_id: ' . auth()->user()->id
            );

            return Resposta::resposta(
                'Ocorreu um erro ao tentar-se buscar os clientes pelo cpf!',
                null,
                200,
                false
            );
        }

    }

    public function buscarClientesPeloEmail($email, $idUsuario) {
        
    }

    public function buscarClientesPeloNome($nome, $idUsuario) {
        
    }

    public function buscarClientesPeloRg($rg, $idUsuario) {
        
    }

    public function buscarTodosClientesUsuario($idUsuario) {
        
        try {

            if (empty($idUsuario)) {

                return Resposta::resposta(
                    'Informe o id do usuário!',
                    null,
                    200,
                    false
                );
            }

            $usuario = Usuario::find($idUsuario);

            if (!$usuario) {

                return Resposta::resposta(
                    'Não existe um usuário cadastrado com esse id no banco de dados!',
                    null,
                    200,
                    false
                );
            }

            $clientesUsuario = $usuario->clientes()
                ->select('id', 'nome', 'telefone', 'email', 'cpf', 'status')
                ->get();

            if (count($clientesUsuario) > 0) {

                return Resposta::resposta(
                    'Clientes encontrados com sucesso!',
                    $clientesUsuario,
                    200,
                    true
                );
            }

            return Resposta::resposta(
                'Você não possui clientes!',
                [],
                200,
                false
            );
        } catch (Exception $e) {
            Log::error(
                'Ocorreu o seguinte erro ao tentar-se buscar os clientes do usuário: ' . $e->getMessage() . ' usuario_id: ' . auth()->user()->id
            );

            return Resposta::resposta(
                'Ocorreu um erro ao tentar-se buscar todos os clientes do usuário!',
                null,
                200,
                false
            );
        }

    }
}