<?php

namespace App\Servico;

use App\Models\Empresa;
use App\Utils\Resposta;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmpresaServico implements IServiceEmpresa
{

    public function cadastrar(Request $requisicao) {

        try {
            $validador = Validator::make($requisicao->all(), [
                'razao_social' => 'required|unique:empresas|max:255',
                'cnpj' => 'required|unique:empresas',
                'data_abertura' => 'required',
                'email' => 'required|unique:empresas|email'
            ],
            [   
                'razao_social.required' => 'Informe a razão social da empresa!',
                'razao_social.unique' => 'Já existe uma empresa cadastrada com esse razão social, informe outra razão social!',
                'razao_social.max' => 'A razão social da empresa deve possuir no máximo 255 caracteres!',
                'cnpj.required' => 'Informe o cnpj da empresa!',
                'cnpj.unique' => 'Já existe uma empresa cadastrada com o cnpj informado, informe outro cnpj!',
                'data_abertura.required' => 'Informe a data de abertura da empresa!',
                'email.required' => 'Informe o e-mail da empresa!',
                'email.unique' => 'Já existe uma empresa cadastrada com esse e-mail, informe outro e-mail para a empresa!',
                'email.email' => 'E-mail inválido!'
            ]);

            if ($validador->fails()) {

                return Resposta::resposta(
                    'Ocorreram erros de validação de dados!',
                    $validador->errors(),
                    200,
                    false
                );
            }

            $empresa = new Empresa();
            $empresa->razao_social = $requisicao->razao_social;
            $empresa->cnpj = $requisicao->cnpj;
            $dataAbertura = new DateTime($requisicao->data_abertura);
            $empresa->data_abertura = $dataAbertura->format('Y-m-d');
            $empresa->email = $requisicao->email;
            $empresa->url_site = $requisicao->url_site;

            if (!$empresa->save()) {

                return Resposta::resposta(
                    'Ocorreu um erro ao tentar-se cadastrar a empresa!',
                    null,
                    200,
                    false
                );
            }

            return Resposta::resposta(
                'Empresa cadastrada com sucesso!',
                $empresa
            );
        } catch (Exception $e) {

            return Resposta::resposta(
                'Ocorreu um erro ao tentar-se cadastrar a empresa!' . $e->getMessage(),
                null,
                200,
                false
            );
        }

    }

    public function buscarEmpresaPeloCnpj(Request $requisicao) {

        try {
            $validador = Validator::make($requisicao->all(), [
                'cnpj' => 'required'
            ],
            [
                'cnpj.required' => 'Informe o cnpj da empresa!'
            ]);

            if ($validador->fails()) {

                return Resposta::resposta(
                    'Ocorreram erros de validação de dados!',
                    $validador->errors(),
                    200,
                    false
                );
            }

            $empresa = Empresa::where('cnpj', $requisicao->cnpj)
                ->get()
                ->toArray();

            if (!$empresa) {

                return Resposta::resposta(
                    'Não existe uma empresa cadastrada no banco de dados com esse cnpj!',
                    null,
                    200,
                    false
                );
            }
                
            return Resposta::resposta(
                'Empresa encontrada com sucesso!',
                $empresa,
                200,
                true                
            );
        } catch (Exception $e) {

            return Resposta::resposta(
                'Ocorreu um erro ao tentar-se buscar a empresa pelo cnpj!',
                null,
                200,
                false
            );
        }

    } 

    public function buscarPeloId($id) {

    }

    public function editarEmpresa(Request $requisicao) {

    }
}