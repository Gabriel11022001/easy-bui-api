<?php

namespace App\Servico;

use App\Models\Categoria;
use App\Models\Empresa;
use App\Models\Produto;
use App\Utils\CalculoUtils;
use App\Utils\Resposta;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ProdutoServico implements IServiceProduto
{

    public function cadastrar(Request $requisicao) {
        
        try {
            $validador = Validator::make($requisicao->all(), [
                'descricao' => 'required|max:255',
                'preco' => 'required|min:0|numeric',
                'qtd_unidades_estoque' => 'required|min:0|numeric',
                'estoque_minimo' => 'nullable|numeric|min:0',
                'estoque_maximo' => 'nullable|numeric|min:0',
                'percentual_desconto' => 'nullable|numeric|min:0|max:100',
                'desconto_dinheiro' => 'nullable|numeric|min:0',
                'categoria_id' => 'required|numeric|min:1',
                'empresa_id' => 'required|numeric|min:1',
                'foto_produto' => 'required'
            ],
            [
                'descricao.required' => 'Informe o nome do produto!',
                'descricao.max' => 'O nome do produto não pode possuir mais que 255 caracteres!',
                'preco.required' => 'Informe o preço do produto!',
                'preco.min' => 'O preço do produto não deve ser menor que R$0.00!',
                'preco.numeric' => 'O preço do produto deve ser um valor numérico!',
                'qtd_unidades_estoque.required' => 'Informe a quantidade de unidades do produto em estoque!',
                'qtd_unidades_estoque.min' => 'A quantidade de unidades do produto em estoque não deve ser menor que 0!',
                'qtd_unidades_estoque.numeric' => 'A quantidade de unidades do produto em estoque deve ser um valor numérico!',
                'estoque_minimo.numeric' => 'O estoque mínimo deve ser um valor numérico!',
                'estoque_minimo.min' => 'O estoque mínimo não deve ser um valor menor que 0!',
                'estoque_maximo.numeric' => 'O estoque máximo deve ser um valor numérico!',
                'estoque_maximo.min' => 'O estoque máximo não deve ser um valor menor que 0!',
                'percentual_desconto.numeric' => 'O percentual de desconto deve ser um valor numérico!',
                'percentual_desconto.min' => 'O percentual de desconto não deve ser um valor menor que 0%!',
                'percentual_desconto.max' => 'O percentual de desconto não deve ser um valor maior que 100%!',
                'desconto_dinheiro.numeric' => 'O desconto em dinheiro deve ser um valor numérico!',
                'desconto_dinheiro.min' => 'O desconto em dinheiro não deve ser um valor menor que R$0.00!',
                'categoria_id.required' => 'Informe o id da categoria do produto!',
                'categoria_id.numeric' => 'O id da categoria deve ser um dado numérico!',
                'categoria_id.min' => 'O id da categoria não deve ser menor que 1!',
                'empresa_id.required' => 'Informe o id da empresa!',
                'empresa_id.numeric' => 'O id da empresa deve ser um valor numérico!',
                'empresa_id.min' => 'O id da empresa deve ser maior ou igual a 1!',
                'foto_produto.required' => 'Forneça uma foto para o produto!'
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

            if ($requisicao->desconto_dinheiro > $requisicao->preco) {

                return Resposta::resposta(
                    'O desconto em dinheiro não deve ser maior que o preço do produto!',
                    null,
                    200,
                    false
                );
            }

            if (!empty($requisicao->estoque_maximo) && !empty($requisicao->estoque_minimo)) {

                if ($requisicao->estoque_minimo >= $requisicao->estoque_maximo) {
                    
                    return Resposta::resposta(
                        'O estoque máximo deve ser maior que o estoque mínimo!',
                        null,
                        200,
                        false
                    );
                }

            }

            if (!empty($requisicao->estoque_maximo)) {

                if ($requisicao->qtd_unidades_estoque > $requisicao->estoque_maximo) {

                    return Resposta::resposta(
                        'O estoque máximo não deve ser menor que a quantidade de unidades do produto em estoque!',
                        null,
                        200,
                        false
                    );
                }

            }

            if (!empty($requisicao->estoque_minimo)) {

                if ($requisicao->estoque_minimo > $requisicao->qtd_unidades_estoque) {

                    return Resposta::resposta(
                        'O estoque mínimo não deve ser maior que a quantidade de unidades do produto em estoque!',
                        null,
                        200,
                        false
                    );
                }

            }

            $empresa = Empresa::find($requisicao->empresa_id);

            if (!$empresa) {

                return Resposta::resposta('Não existe uma empresa cadastrada com esse id!', null, 200, false);
            }

            $produtoComDescricaoInformada = Produto::where('descricao', mb_strtoupper($requisicao->descricao))
                ->where('empresa_id', $requisicao->empresa_id)
                ->get()
                ->toArray();

            if ($produtoComDescricaoInformada) {

                return Resposta::resposta('A empresa a qual você faz parte, já possui um produto cadastrado com essa descrição!', null, 200, false);
            }

            $categoria = Categoria::find($requisicao->categoria_id);

            if (!$categoria) {

                return Resposta::resposta('Não existe uma categoria cadastrada com esse id!', null, 200, false);
            }

            $produto = new Produto();
            $produto->descricao = mb_strtoupper($requisicao->descricao);
            $produto->preco = number_format($requisicao->preco, 2);
            $produto->qtd_unidades_estoque = intval($requisicao->qtd_unidades_estoque);

            if (!empty($requisicao->estoque_maximo)) {
                $produto->estoque_maximo = intval($requisicao->estoque_maximo);
            }

            if (!empty($requisicao->estoque_minimo)) {
                $produto->estoque_minimo = intval($requisicao->estoque_minimo);
            }

            if (!empty($requisicao->percentual_desconto)) {
                $produto->percentual_desconto = number_format($requisicao->percentual_desconto, 2);
            }

            if (!empty($requisicao->desconto_dinheiro)) {
                $produto->desconto_dinheiro = number_format($requisicao->desconto_dinheiro, 2);
            }

            $produto->empresa_id = intval($requisicao->empresa_id);
            $produto->categoria_id = intval($requisicao->categoria_id);
            $produto->foto_produto = $requisicao->foto_produto;

            if ($produto->save()) {
                Log::debug('Produto cadastrado com sucesso! usuario_id: ' . auth()->user()->id, $produto->toArray());

                return Resposta::resposta('Produto cadastrado com sucesso!', Produto::find($produto->id), 201, true);
            }

            return Resposta::resposta('Ocorreu um erro ao tentar-se cadastrar o produto!', null, 200, false);
        } catch (Exception $e) {
            Log::error(
                'Ocorreu o seguinte erro ao tentar-se cadastrar o produto: ' . $e->getMessage() . ' usuario_id: ' . auth()->user()->id,
                $requisicao->all()
            );

            return Resposta::resposta('Ocorreu um erro ao tentar-se cadastrar o produto!', null, 200, false);
        }

    }

    public function editarProduto(Request $requisicao) {
        
    }

    public function buscarPeloId($id) {
        
        try {

            if (empty($id)) {

                return Resposta::resposta(
                    'Informe o id do produto!',
                    null,
                    200,
                    false
                );
            }

            $produto = Produto::find($id);

            if (!$produto) {

                return Resposta::resposta('Não existe um produto cadastrado no banco de dados com esse id!', null, 200, false);
            }

            $categoriaProduto = $produto->categoria()->get();
            $produto['categoria'] = $categoriaProduto;
            CalculoUtils::calcularValorDescontoProduto($produto);

            return Resposta::resposta(
                'Produto encontrado com sucesso!',
                $produto,
                200,
                true
            );
        } catch (Exception $e) {
            Log::error(
                'Ocorreu o seguinte erro ao tentar-se buscar o produto pelo id: ' . $e->getMessage() . ' usuario_id: ' . auth()->user()->id
            );

            return Resposta::resposta(
                'Ocorreu um erro ao tentar-se buscar o produto pelo id!',
                null,
                200,
                false
            );
        }

    }

    public function buscarProdutosAbaixoEstoqueMinimo($idEmpresa) {
        
        try {

            if (empty($idEmpresa)) {

                return Resposta::resposta('Informe o id da empresa!', null, 200, false);
            }

            $produtos = $this->obterProdutosAbaixoEstoqueMinimoEmpresa($idEmpresa);

            if (count($produtos) > 0) {
                CalculoUtils::calcularDescontosProdutos($produtos);

                return Resposta::resposta(
                    'Produtos abaixo do estoque mínimo encontrados com sucesso!',
                    $produtos,
                    200,
                    true
                );
            }

            return Resposta::resposta(
                'Não existem produtos abaixo do estoque mínimo!',
                [],
                200,
                true
            );
        } catch (Exception $e) {

            return Resposta::resposta(
                'Ocorreu um erro ao tentar-se buscar os produtos abaixo do estoque mínimo!' . $e->getMessage(),
                null,
                200,
                false
            );
        }

    }

    private function obterProdutosAbaixoEstoqueMinimoEmpresa($idEmpresa) {
        $colunas = [
            'produtos.id',
            'produtos.descricao AS descricao_produto',
            'produtos.status',
            'produtos.preco',
            'produtos.qtd_unidades_estoque',
            'produtos.estoque_minimo',
            'produtos.percentual_desconto',
            'categorias.descricao AS categoria'
        ];

        return DB::table('produtos')
            ->join('categorias', 'categorias.id', '=', 'produtos.categoria_id')
            ->select($colunas)
            ->where('produtos.empresa_id', $idEmpresa)
            ->where('produtos.qtd_unidades_estoque', '<', 'produtos.estoque_minimo')
            ->whereNotNull('produtos.estoque_minimo')
            ->get();
    }

    public function buscarProdutosAtivosEmpresa($idEmpresa) {
        
    }

    public function buscarProdutosEntrePrecos(Request $requisicao) {
        
    }

    public function buscarProdutosPelaCategoria($idCategoria, $idEmpresa) {
        
        try {

            if (empty($idEmpresa)) {

                return Resposta::resposta('Informe o id da empresa!', null, 200, false);
            }
            
            if (empty($idCategoria)) {

                return Resposta::resposta('Informe o id da categoria!', null, 200, false);
            }

            $empresa = Empresa::find($idEmpresa);

            if (!$empresa) {

                return Resposta::resposta('Não existe uma empresa cadastrada com o id informado!', null, 200, false);
            }

            $categoria = Categoria::where('id', $idCategoria)
                ->where('empresa_id', $idEmpresa)
                ->get();

            if (!$categoria) {

                return Resposta::resposta('Não existe uma categoria cadastrada para a sua empresa com o id informado!', null, 200, false);
            }

            $produtos = DB::table('produtos')
                ->join('categorias', 'produtos.categoria_id', '=', 'categorias.id')
                ->select(
                    'produtos.id',
                    'produtos.descricao AS produto',
                    'produtos.preco',
                    'produtos.qtd_unidades_estoque',
                    'produtos.status',
                    'produtos.percentual_desconto',
                    'categorias.descricao AS categoria'
                )
                ->where('produtos.categoria_id', $idCategoria)
                ->get();
            
            if (count($produtos) > 0) {
                CalculoUtils::calcularDescontosProdutos($produtos);

                return Resposta::resposta(
                    'Produtos encontrados com sucesso!',
                    $produtos,
                    200,
                    true
                );
            }

            return Resposta::resposta(
                'Não existem produtos cadastrados no banco de dados!',
                [],
                200,
                false
            );
        } catch (Exception $e) {

            return Resposta::resposta('Ocorreu um erro ao tentar-se buscar os produtos pela categoria!', null, 200, false);
        }

    }

    public function buscarProdutosPelaDescricao(Request $requisicao) {
        
        try {
            $validador = Validator::make($requisicao->all(),
            [
                'empresa_id' => 'required|numeric|min:1'
            ],
            [
                'empresa_id.required' => 'Informe o id da empresa!',
                'empresa_id.numeric' => 'O id da empresa deve ser um valor numérico!',
                'empresa_id.min' => 'O id da empresa não deve ser menor que 1!'
            ]);

            if ($validador->fails()) {

                return Resposta::resposta(
                    'Ocorreram os seguintes erros de validação de dados!',
                    $validador->errors(),
                    200,
                    false
                );
            }

            $descricao = $requisicao->descricao;
            $descricao = mb_strtoupper($descricao);
            $produtos = DB::table('produtos')
                ->join('categorias', 'produtos.categoria_id', '=', 'categorias.id')
                ->select(
                    'produtos.id',
                    'produtos.descricao AS produto',
                    'produtos.preco',
                    'produtos.qtd_unidades_estoque',
                    'produtos.status',
                    'produtos.percentual_desconto',
                    'categorias.descricao AS categoria'
                )
                ->where('produtos.descricao', 'like', '%' . $descricao . '%')
                ->where('produtos.empresa_id', $requisicao->empresa_id)
                ->get();
            
            if (count($produtos) >  0) {
                CalculoUtils::calcularDescontosProdutos($produtos);
            }    

            return Resposta::resposta(
                'Foram encontrados os seguintes dados!',
                $produtos,
                200,
                true
            );
        } catch (Exception $e) {

            return Resposta::resposta(
                'Ocorreu um erro ao tentar-se buscar os produtos pela descrição!',
                null,
                200,
                false
            );
        }

    }

    public function buscarTodosProdutosEmpresa($idEmpresa) {
        
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
                    'Não existe uma empresa cadastrada no banco de dados com esse id!',
                    null,
                    200,
                    false
                );
            }

            $produtos = $this->obterProdutosComCategoriaDaEmpresa($idEmpresa);

            if (count($produtos) > 0) {
                CalculoUtils::calcularDescontosProdutos($produtos);

                return Resposta::resposta(
                    'Produtos da empresa encontrados com sucesso!',
                    $produtos,
                    200,
                    true
                );
            }

            return Resposta::resposta(
                'A empresa não possui produtos cadastrados no banco de dados!',
                [],
                200,
                false
            );
        } catch (Exception $e) {

            return Resposta::resposta(
                'Ocorreu um erro ao tentar-se buscar os produtos da empresa!',
                null,
                200,
                false
            );
        }

    }

    private function obterProdutosComCategoriaDaEmpresa($idEmpresa) {
        $colunas = [
            'produtos.descricao AS produto',
            'categorias.descricao AS categoria',
            'produtos.id',
            'produtos.preco',
            'produtos.qtd_unidades_estoque',
            'produtos.empresa_id',
            'produtos.status',
            'produtos.percentual_desconto'
        ];

        return DB::table('produtos')
            ->join('categorias', 'produtos.categoria_id', '=', 'categorias.id')
            ->select($colunas)
            ->where('produtos.empresa_id', $idEmpresa)
            ->get();
    }
    
    public function alterarStatusProduto(Request $requisicao) {
        
    }

    public function buscarProdutosZeradosEstoque($idEmpresa) {
        
    }
}