<?php

namespace App\Servico;

use App\Models\Categoria;
use App\Models\Empresa;
use App\Utils\Resposta;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoriaServico implements IServiceCategoria
{

    public function cadastrar(Request $requisicao) {
        
        try {
            $validador = Validator::make($requisicao->all(), [
                'descricao' => 'required|max:255',
                'empresa_id' => 'required|numeric|min:1'
            ],
            [
                'descricao.required' => 'Informe a descrição da categoria!',
                'descricao.max' => 'A descrição da categoria deve possuir no máximo 255 caracteres!',
                'empresa_id.required' => 'Informe o id da empresa relacionada a categoria!',
                'empresa_id.numeric' => 'O id da empresa deve ser um valor numérico!',
                'empresa_id.min' => 'O id da empresa deve ser maior ou igual a 1!'
            ]);

            if ($validador->fails()) {

                return Resposta::resposta(
                    'Ocorreram erros de validação de dados!',
                    $validador->errors(),
                    200,
                    false
                );
            }

            $categoriaEmpresaComDescricaoInformada = Categoria::where('descricao', $requisicao->descricao)
                ->where('empresa_id', $requisicao->empresa_id)
                ->get()
                ->toArray();
            
            if (count($categoriaEmpresaComDescricaoInformada) > 0) {

                return Resposta::resposta('Já existe uma categoria cadastrada para a sua empresa com essa descrição!', null, 200, false);
            }    

            $empresa = Empresa::find($requisicao->empresa_id);

            if (!$empresa) {

                return Resposta::resposta('Não existe uma empresa cadastrada com esse id!', null, 200, false);
            }

            $categoria = new Categoria();
            $categoria->descricao = $requisicao->descricao;
            $categoria->empresa_id = $requisicao->empresa_id;

            if ($categoria->save()) {

                return Resposta::resposta('Categoria cadastrada com sucesso!', $categoria, 201, true);
            }

            return Resposta::resposta('Ocorreu um erro ao tentar-se cadastrar a categoria!', null, 200, false);
        } catch (Exception $e) {

            return Resposta::resposta(
                'Ocorreu um erro ao tentar-se cadastrar a categoria!',
                null,
                200,
                false
            );
        }

    }

    public function editarCategoria(Request $requisicao) {
        
    }

    public function buscarPeloId($id) {
        
        try {

            if (empty($id)) {

                return Resposta::resposta('Informe o id da categoria!', null, 200, false);
            }

            $categoria = Categoria::find($id);

            if (!$categoria) {

                return Resposta::resposta('Não existe uma categoria cadastrada com esse id!', null, 200, false);
            }

            return Resposta::resposta('Categoria encontrada com sucesso!', $categoria, 200, true);
        } catch (Exception $e) {

            return Resposta::resposta(
                'Ocorreu um erro ao tentar-se buscar a categoria pelo id!',
                null,
                200,
                false
            );
        }

    }

    public function buscarTodasCategoriasEmpresa($idEmpresa) {
        
        try {

            if (empty($idEmpresa)) {

                return Resposta::resposta('Informe o id da empresa!', null, 200, false);
            }

            $empresa = Empresa::find($idEmpresa);

            if (!$empresa) {

                return Resposta::resposta('Não existe uma empresa cadastrada com esse id!', null, 200, false);
            }

            $categoriasEmpresa = $empresa->categorias()
                ->get();

            if (count($categoriasEmpresa) === 0) {

                return Resposta::resposta('A empresa não possui categorias!', [], 200, false);
            }
            
            return Resposta::resposta('Categorias encontradas com sucesso!', $categoriasEmpresa, 200, true);
        } catch (Exception $e) {

            return Resposta::resposta(
                'Ocorreu um erro ao tentar-se buscar as categorias da empresa!' . $e->getMessage(),
                null,
                200,
                false
            );
        }

    }

    public function alterarStatusCategoria(Request $requisicao) {
        
        try {
            $validador = Validator::make($requisicao->all(), [
                'categoria_id' => 'required|numeric|min:1'
            ],
            [
                'categoria_id.required' => 'Informe o id da categoria',
                'categoria_id.numeric' => 'O id da categoria deve ser um valor numérico!',
                'categoria_id.min' => 'O id da categoria deve ser maior ou igual a 1!'
            ]);

            if ($validador->fails()) {

                return Resposta::resposta(
                    'Ocorreram erros de validação de dados!',
                    $validador->errors(),
                    200,
                    false
                );
            }

            $categoria = Categoria::find($requisicao->categoria_id);

            if (!$categoria) {

                return Resposta::resposta(
                    'Não existe uma categoria cadastrada com esse id!',
                    null,
                    200,
                    false
                );
            }

            $categoria->status = !$categoria->status;

            if (!$categoria->save()) {

                return Resposta::resposta(
                    'Ocorreu um erro ao tentar-se alterar o status da categoria!',
                    null,
                    200,
                    false
                );
            }

            return Resposta::resposta(
                'O status da categoria foi alterado com sucesso!',
                $categoria,
                200,
                true
            );
        } catch (Exception $e) {

            return Resposta::resposta('Ocorreu um erro ao tentar-se alterar o status da categoria!', null, 200, false);
        }

    }
}