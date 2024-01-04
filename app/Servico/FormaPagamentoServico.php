<?php

namespace App\Servico;

use App\Models\Empresa;
use App\Models\FormaPagamento;
use App\Utils\Resposta;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class FormaPagamentoServico implements IServiceFormaPagamento
{

    public function cadastrar(Request $requisicao) {
        
        try {
            $validador = Validator::make($requisicao->all(),
            [
                'descricao' => 'required',
                'permite_parcelamento' => 'required|boolean',
                'empresa_id' => 'required|numeric|min:1'
            ],
            [
                'descricao.required' => 'Informe a descrição da forma de pagamento!',
                'permite_parcelamento.required' => 'Informe se a forma de pagamento permite parcelamento ou não!',
                'permite_parcelamento.boolean' => 'A coluna informando se a forma de pagamento ou não deve ser um dado igual a true ou false!',
                'empresa_id.required' => 'Informe o id da empresa!',
                'empresa_id.numeric' => 'O id da empresa deve ser um valor numérico!',
                'empresa_id.min' => 'O id da empresa deve ser um valor maior ou igual a 1!'
            ]);

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

            $empresa = Empresa::find($requisicao->empresa_id);

            if (!$empresa) {

                return Resposta::resposta(
                    'Não existe uma empresa cadastrada no banco de dados com esse id!',
                    null,
                    200,
                    false
                );
            }

            $formaPagamentoComDescricaoInformada = FormaPagamento::where('descricao', mb_strtoupper($requisicao->descricao))
                ->where('empresa_id', $requisicao->empresa_id)
                ->get()
                ->first();
            
            if (!empty($formaPagamentoComDescricaoInformada)) {

                return Resposta::resposta(
                    'A empresa a qual você faz parte já possui uma forma de pagamento cadastrada com essa descrição!',
                    null,
                    200,
                    false
                );
            }
            
            $formaPagamento = new FormaPagamento();
            $formaPagamento->descricao = mb_strtoupper($requisicao->descricao);
            $formaPagamento->empresa_id = $requisicao->empresa_id;
            $formaPagamento->permite_parcelamento = $requisicao->permite_parcelamento;

            if ($formaPagamento->save()) {
                Log::debug(
                    'Forma de pagamento cadastrada com sucesso!',
                    $formaPagamento->toArray()
                );

                return Resposta::resposta(
                    'Forma de pagamento cadastrada com sucesso!',
                    $formaPagamento,
                    201,
                    true
                );
            }

            Log::error('Ocorreu um erro ao tentar-se cadastrar a forma de pagamento! usuario_id: ' . auth()->user()->id);

            return Resposta::resposta(
                'Ocorreu um erro ao tentar-se cadastrar a forma de pagamento!',
                null,
                200,
                false
            );
        } catch (Exception $e) {
            Log::error('Ocorreu o seguinte erro ao tentar-se cadastrar a forma de pagamento: ' . $e->getMessage() . ' usuario_id: ' . auth()->user()->id);

            return Resposta::resposta(
                'Ocorreu um erro ao tentar-se cadastrar a forma de pagamento!',
                null,
                200,
                false
            );
        }

    }

    public function buscarPeloId($id) {
        
    }

    public function buscarTodasFormasPagamentoEmpresa($idEmpresa) {
        
        try {

            if (empty($idEmpresa)) {

                return Resposta::resposta(
                    'Informe o id da empresa!',
                    null,
                    200,
                    false
                );
            }

            $formasPagamento = FormaPagamento::where('empresa_id', $idEmpresa)
                ->get();
            
            if (count($formasPagamento) > 0) {

                return Resposta::resposta(
                    'Formas de pagamento encontradas com sucesso!',
                    $formasPagamento,
                    200,
                    true
                );
            }
            
            return Resposta::resposta(
                'A empresa em questão não possui formas de pagamento cadastradas!',
                [],
                200,
                false
            );
        } catch (Exception $e) {
            Log::error(
                'Ocorreu o seguinte erro ao tentar-se buscar as formas de pagamento: ' . $e->getMessage() . ' usuario_id: ' . auth()->user()->id
            );

            return Resposta::resposta(
                'Ocorreu um erro ao tentar-se buscar as formas de pagamento!',
                null,
                200,
                false
            );
        }

    }

    public function alterarStatusFormaPagamento($idFormaPagamento, $idEmpresa) {
        
    }

    public function buscarTodasFormasPagamentoEmpresaAtivas($idEmpresa) {
        
        try {

            if (empty($idEmpresa)) {

                return Resposta::resposta(
                    'Informe o id da empresa!',
                    null,
                    200,
                    false
                );
            }

            $empresa = Empresa::find($idEmpresa);

            if (!$empresa) {

                return Resposta::resposta(
                    'Não existe uma empresa cadastrada com esse id!',
                    null,
                    200,
                    false
                );
            }

            $formasPagamento = FormaPagamento::where('empresa_id', $idEmpresa)
                ->where('status', true)
                ->get();

            if (count($formasPagamento) === 0) {

                return Resposta::resposta(
                    'A empresa não possui formas de pagamento ativas!',
                    [],
                    200,
                    false
                );
            }
            
            return Resposta::resposta(
                'Formas de pagamento ativas encontradas com sucesso!',
                $formasPagamento,
                200,
                true
            );
        } catch (Exception $e) {
            Log::error('Ocorreu o seguinte erro ao tentar-se buscar as formas de pagamento ativas: ' . $e->getMessage() . ' usuarios_id: ' . auth()->user()->id);

            return Resposta::resposta(
                'Ocorreu um erro ao tentar-se buscar as formas de pagamento ativas!',
                null,
                200,
                false
            );
        }

    }

    public function editarFormaPagamento(Request $requisicao) {
        
    }
}