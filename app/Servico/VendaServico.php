<?php

namespace App\Servico;

use App\Models\Cliente;
use App\Models\FormaPagamento;
use App\Models\ItemVenda;
use App\Models\Parcela;
use App\Models\Produto;
use App\Models\Usuario;
use App\Models\Venda;
use App\Utils\CalculoUtils;
use App\Utils\Resposta;
use DateInterval;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class VendaServico implements IServiceVenda
{
    private const LIMITE_MESES_CONSULTA = 3;
    private $validadoresCadastroVenda = [
        'parcelado' => 'required|boolean',
        'numero_parcelas' => 'nullable|numeric|min:1',
        'valor_pago' => 'nullable|numeric|min:0',
        'usuario_id' => 'required|numeric|min:1',
        'cliente_id' => 'required|numeric|min:1',
        'forma_pagamento_id' => 'required|numeric|min:1',
        'items' => 'required|array',
        'items.*.produto_id' => 'required|numeric|min:1',
        'items.*.qtd_unidades' => 'required|numeric|min:1'
    ];
    private $mensagensValidadoresCadastroVenda = [
        'numero_parcelas.numeric' => 'O número de parcelas deve ser um valor numérico!',
        'numero_parcelas.min' => 'O número de parcelas deve ser um valor maior ou igual a 1!',
        'parcelado.required' => 'Informe se a venda será parcelada ou não!',
        'parcelado.boolean' => 'O campo informando se a venda será parcelada ou não deve ser igual a true ou false!',
        'valor_pago.numeric' => 'O valor pago deve ser um dado numérico!',
        'valor_pago.min' => 'O valor pago deve ser um valor maior ou igual a R$0.00!',
        'usuario_id.required' => 'Informe o id do usuário!',
        'usuario_id.numeric' => 'O id do usuário deve ser um valor numérico!',
        'usuario_id.min' => 'O id do usuário não deve ser um valor menor que 1!',
        'cliente_id.required' => 'Informe o id do cliente!',
        'cliente_id.numeric' => 'O id do cliente deve ser um valor numérico!',
        'cliente_id.min' => 'O id do cliente não deve ser um valor menor que 1!',
        'forma_pagamento_id.required' => 'Informe o id da forma de pagamento!',
        'forma_pagamento_id.numeric' => 'O id da forma de pagamento deve ser um valor numérico!',
        'forma_pagamento_id.min' => 'O id da forma de pagamento não deve ser um valor menor que 1!',
        'items.required' => 'Para realizar a venda, é necessário comprar pelo menos um produto!',
        'items.array' => 'A lista de items da venda deve ser um dado do tipo array!',
        'items.*.produto_id.required' => 'Informe o id do produto!',
        'items.*.produto_id.numeric' => 'O id do produto deve ser um valor numérico!',
        'items.*.produto_id.min' => 'O id do produto não deve ser um valor menor que 1!',
        'items.*.qtd_unidades.required' => 'Informe quantas unidades do produto serão vendidas!',
        'items.*.qtd_unidades.numeric' => 'A quantidade de unidades do produto deve ser um valor numérico!',
        'items.*.qtd_unidades.min' => 'A quantidade de unidades do produto não deve ser menor que 1!'
    ];

    public function cadastrar(Request $requisicao) {
        DB::beginTransaction();

        try {
            $validador = Validator::make(
                $requisicao->all(),
                $this->validadoresCadastroVenda,
                $this->mensagensValidadoresCadastroVenda
            );

            if ($validador->fails()) {

                return Resposta::resposta(
                    'Ocorreram erros de validação de dados!',
                    $validador->errors(),
                    200,
                    false
                );
            }

            $usuario = Usuario::find($requisicao->usuario_id);

            if (!$usuario) {

                return Resposta::resposta('Não existe um usuário cadastrado no banco de dados com esse id!', null, 200, false);
            }

            $cliente = Cliente::find($requisicao->cliente_id);

            if (!$cliente) {

                return Resposta::resposta(
                    'Não existe um cliente cadastrado com esse id no banco de dados!',
                    null,
                    200,
                    false
                );
            }

            if (!$cliente->status) {

                return Resposta::resposta(
                    'O cliente não está ativo!',
                    null,
                    200,
                    false
                );
            }

            $formaPagamento = FormaPagamento::find($requisicao->forma_pagamento_id);

            if (!$formaPagamento) {

                return Resposta::resposta(
                    'Não existe uma forma de pagamento cadastrada com esse id!',
                    null,
                    200,
                    false
                );
            }

            if (!$formaPagamento->status) {

                return Resposta::resposta(
                    'A forma de pagamento ' . $formaPagamento->descricao . ' não está ativa!',
                    null,
                    200,
                    false
                );
            }

            $errosProdutosCarrinho = $this->validarProdutosVenda($requisicao->items);

            if (count($errosProdutosCarrinho) > 0) {

                return Resposta::resposta(
                    'Ocorreram erros de validação de dados do carrinho de compras!',
                    $errosProdutosCarrinho,
                    200,
                    false
                );
            }

            $valorTotalVenda = CalculoUtils::calcularValorTotalVenda($requisicao->items);
            $venda = new Venda();
            $parcelas = [];
            $venda->valor_total = $valorTotalVenda;
            $venda->cliente_id = $requisicao->cliente_id;
            $venda->usuario_id = $requisicao->usuario_id;
            $venda->forma_pagamento_id = $requisicao->forma_pagamento_id;
            $dataCadastroVenda = new DateTime('now');
            $venda->data_cadastro_venda = $dataCadastroVenda->format('Y-m-d H:i:s');

            if (!empty($requisicao->valor_pago)) {

                if ($requisicao->valor_pago > $valorTotalVenda) {
                    
                    return Resposta::resposta(
                        'O valor pago não pode ser maior que o valor total da venda!',
                        null,
                        200,
                        false
                    );
                }

                $venda->valor_pago = $requisicao->valor_pago;
                $venda->valor_ainda_falta_pagar = $valorTotalVenda - $requisicao->valor_pago;
            }

            if ($requisicao->parcelado) {
                $numeroParcelas = $requisicao->numero_parcelas;
                $parcelas = $this->gerarParcelas($valorTotalVenda, $numeroParcelas);
            }

            if (count($parcelas) === 0) {
                $dataPagamentoParcela = $dataCadastroVenda->add(new DateInterval('P1M'));
                $venda->data_limite_pagamento = $dataPagamentoParcela->format('Y-m-d H:i:s');
            } else {
                $ultimaParcela = $parcelas[count($parcelas) - 1];
                $venda->data_limite_pagamento = $ultimaParcela['data_limite_pagamento'];
            }

            if (!$venda->save()) {
                DB::rollBack();

                return Resposta::resposta(
                    'Ocorreu um erro ao tentar-se realizar a venda!',
                    null,
                    200,
                    false
                );
            }

            // salvando os items da venda no banco de dados
            foreach ($requisicao->items as $item) {
                $itemVenda = new ItemVenda();
                $itemVenda->produto_id = $item['produto_id'];
                $itemVenda->qtd_unidades = $item['qtd_unidades'];
                $itemVenda->venda_id = $venda->id;
                $produtoVenda = Produto::find($item['produto_id']);
                
                if ($produtoVenda->desconto_dinheiro != 0) {
                    $itemVenda->preco_produto = $produtoVenda->preco - $produtoVenda->desconto_dinheiro;
                } else {
                    $itemVenda->preco_produto = $produtoVenda->preco;
                }

                $novaQuantidadeProduto = $produtoVenda->qtd_unidades_estoque - $item['qtd_unidades'];
                $produtoVenda->qtd_unidades_estoque = $novaQuantidadeProduto;

                if (!$produtoVenda->save()) {
                    DB::rollBack();

                    return Resposta::resposta(
                        'Ocorreu um erro ao tentar-se realizar a venda!',
                        null,
                        200,
                        false
                    );
                }

                if (!$itemVenda->save()) {
                    DB::rollBack();

                    return Resposta::resposta(
                        'Ocorreu um erro ao tentar-se realizar a venda!',
                        null,
                        200,
                        false
                    );
                }

            }

            // salvando as parcelas
            foreach ($parcelas as $parcela) {
                $parcelaVenda = new Parcela();
                $parcelaVenda->numero_parcela = $parcela['numero_parcela'];
                $parcelaVenda->valor = $parcela['valor'];
                $parcelaVenda->data_limite_pagamento = $parcela['data_limite_pagamento'];
                $parcelaVenda->venda_id = $venda->id;
                
                if (!$parcelaVenda->save()) {
                    DB::rollBack();

                    return Resposta::resposta(
                        'Ocorreu um erro ao tentar-se realizar a venda!',
                        null,
                        200,
                        false
                    );
                }
                
            }

            DB::commit();

            return Resposta::resposta(
                'Venda finalizada com sucesso!',
                [
                    'id' => $venda->id,
                    'cliente' => [
                        'nome' => $cliente->nome,
                        'cpf' => $cliente->cpf
                    ],
                    'forma_pagamento' => $formaPagamento->descricao,
                    'valor_total' => $venda->valor_total,
                    'parcelado' => $requisicao->parcelado,
                    'valor_pago' => $venda->valor_pago,
                    'valor_ainda_falta_pagar' => $venda->valor_ainda_falta_pagar,
                    'items' => $requisicao->items,
                    'parcelas' => $parcelas
                ]
            );
        } catch (Exception $e) {
            DB::rollBack();

            return Resposta::resposta(
                'Ocorreu um erro ao tentar-se realizar a venda!' . $e->getMessage(),
                null,
                200,
                false
            );
        }
    }

    private function gerarParcelas($valorTotal, $numeroParcelas) {
        $parcelas = [];

        for ($i = 0; $i < $numeroParcelas; $i++) {
            $parcela = [];
            $dataAtual = new DateTime('now');
            // calcular data de pagamento da parcela
            $dataPagamentoParcela = $dataAtual->add(new DateInterval('P' . ($i + 1) . 'M'));
            $parcela['data_limite_pagamento'] = $dataPagamentoParcela->format('Y-m-d H:i:s');
            $parcela['numero_parcela'] = $i + 1;
            $parcela['valor'] = $valorTotal / $numeroParcelas; 
            $parcelas[] = $parcela;
        }

        return $parcelas;
    }

    private function validarProdutosVenda($items) {
        $errosProdutosCarrinho = [];

        foreach ($items as $item) {
            $produto = Produto::find($item['produto_id']);

            if (!$produto) {
                $errosProdutosCarrinho[]['produto_id'] = 'Não existe um produto cadastrado com o id ' . $item['produto_id'];
            } elseif ($this->validarDuplicidadeProdutoCarrinho($produto->id, $items)) {
                $errosProdutosCarrinho[]['produto'] = 'O produto ' . $produto->descricao . ' está duplicado no carrinho de compras!';
                break;
            } elseif (!$produto->status) {
                $errosProdutosCarrinho[]['produto_id'] = 'O produto' . $produto->descricao . 'não está ativo!';
            } elseif ($item['qtd_unidades'] > $produto->qtd_unidades_estoque) {
                $errosProdutosCarrinho[]['qtd_unidades'] = 'O produto ' . $produto->descricao . ' não possui quantidade suficiente de unidades em estoque!';
            }

        }

        return $errosProdutosCarrinho;
    }

    private function validarDuplicidadeProdutoCarrinho($idProduto, $itemsCarrinho) {
        $quantidadeVezesApareceCarrinho = 0;

        foreach ($itemsCarrinho as $item) {

            if ($item['produto_id'] === $idProduto) {
                $quantidadeVezesApareceCarrinho++;
            }

        }

        return $quantidadeVezesApareceCarrinho > 1;
    }

    public function buscarPeloId($id) {
        
        try {

            if (empty($id)) {

                return Resposta::resposta(
                    'Informe o id da venda!',
                    null,
                    200,
                    false
                );
            }
            
            $colunasConsulta = [
                'vendas.id',
                'vendas.valor_total',
                'vendas.data_cadastro_venda',
                'vendas.status',
                'vendas.data_limite_pagamento',
                'vendas.parcelado',
                'vendas.valor_pago',
                'vendas.valor_ainda_falta_pagar',
                'clientes.nome AS nome_cliente',
                'clientes.cpf AS cpf_cliente',
                'forma_pagamentos.descricao AS forma_pagamento'
            ];
            $venda = DB::table('vendas')
                ->join('clientes', 'clientes.id', '=', 'vendas.cliente_id')
                ->join('forma_pagamentos', 'forma_pagamentos.id', '=', 'vendas.forma_pagamento_id')
                ->where('vendas.id', $id)
                ->select($colunasConsulta)
                ->get()
                ->first();

            if (!$venda) {

                return Resposta::resposta(
                    'Não existe uma venda cadastrada no banco de dados com esse id!',
                    null,
                    200,
                    false
                );
            }
            
            return Resposta::resposta(
                'Venda encontrada com sucesso!',
                $venda,
                200,
                true
            );
        } catch (Exception $e) {
            Log::error('Ocorreu o seguinte erro ao tentar-se buscar a venda pelo id: ' . $e->getMessage() . ' usuario_id: ' . auth()->user()->id);

            return Resposta::resposta(
                'Ocorreu um erro ao tentar-se buscar a venda pelo id!',
                null,
                200,
                false
            );
        }

    }

    public function buscarVendasCliente($idUsuario, $idCliente) {

        try {

            if (empty($idUsuario)) {

                return Resposta::resposta(
                    'Informe o id do usuário!',
                    null,
                    200,
                    false
                );
            }

            if (empty($idCliente)) {

                return Resposta::resposta(
                    'Informe o id do cliente!',
                    null,
                    200,
                    false
                );
            }

            $usuario = Usuario::find($idUsuario);
            $cliente = Cliente::find($idCliente);

            if (!$usuario) {

                return Resposta::resposta(
                    'Não existe um usuário cadastrado no banco de dados com esse id!',
                    null,
                    200,
                    false
                );
            }

            if (!$cliente) {

                return Resposta::resposta(
                    'Não existe um cliente cadastrado no banco de dados com esse id!',
                    null,
                    200,
                    false
                );
            }

            if ($cliente->usuario_id != $idUsuario) {

                return Resposta::resposta(
                    'Esse cliente não é um cliente do usuário em questão!',
                    null,
                    200,
                    false
                );
            }

            $colunasConsulta = [
                'vendas.id',
                'vendas.valor_total',
                'vendas.data_cadastro_venda',
                'vendas.status',
                'vendas.data_limite_pagamento',
                'vendas.parcelado',
                'vendas.valor_pago',
                'vendas.valor_ainda_falta_pagar',
                'clientes.nome AS nome_cliente',
                'clientes.cpf AS cpf_cliente',
                'forma_pagamentos.descricao AS forma_pagamento'
            ];
            $vendasCliente = DB::table('vendas')
                ->select($colunasConsulta)
                ->join('usuarios', 'usuarios.id', '=', 'vendas.usuario_id')
                ->join('clientes', 'clientes.id', '=', 'vendas.cliente_id')
                ->join('forma_pagamentos', 'forma_pagamentos.id', '=', 'vendas.forma_pagamento_id')
                ->where('vendas.usuario_id', $idUsuario)
                ->where('vendas.cliente_id', $idCliente)
                ->get();

            if (count($vendasCliente) === 0) {

                return Resposta::resposta(
                    'O cliente em questão não possui vendas!',
                    [],
                    200,
                    false
                );
            }

            return Resposta::resposta(
                'Vendas encontradas com sucesso!',
                $vendasCliente,
                200,
                false
            );
        } catch (Exception $e) {

            return Resposta::resposta(
                'Ocorreu um erro ao tentar-se buscar as vendas do cliente!',
                null,
                200,
                false
            );
        }

    }

    public function buscarVendasUsuario($idUsuario) {

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
                    'Não existe um usuário cadastrado no banco de dados com esse id!',
                    null,
                    200,
                    false
                );
            }

            $dataAtual = new DateTime('now');
            $dataUltimosMesesConsulta = $dataAtual->add(new DateInterval('P' . self::LIMITE_MESES_CONSULTA . 'M'));
            $vendas = DB::table('vendas')
                ->join('clientes', 'clientes.id', '=', 'vendas.cliente_id')
                ->join('forma_pagamentos', 'forma_pagamentos.id', '=', 'vendas.forma_pagamento_id')
                ->select(
                    'vendas.id',
                    'vendas.valor_total',
                    'vendas.status',
                    'clientes.nome AS cliente',
                    'forma_pagamentos.descricao AS forma_pagamento'
                )
                ->where('vendas.usuario_id', $idUsuario)
                ->where('vendas.data_cadastro_venda', '<=', $dataUltimosMesesConsulta->format('Y-m-d H:i:s'))
                ->orderBy('vendas.data_cadastro_venda', 'ASC')
                ->get();
            
            if (count($vendas) === 0) {

                return Resposta::resposta(
                    'Não existem vendas cadastradas no banco de dados!',
                    [],
                    200,
                    false
                );
            }
            
            return Resposta::resposta(
                'Vendas encontradas com sucesso!',
                $vendas,
                200,
                true
            );
        } catch (Exception $e) {
            Log::error(
                'Ocorreu o seguinte erro ao tentar-se buscar os dados das vendas do usuário: ' . $e->getMessage() . ' usuario_id: ' . auth()->user()->id
            );

            return Resposta::resposta(
                'Ocorreu um erro ao tentar-se buscar as vendas!',
                null,
                200,
                false
            );
        }

    }

    public function buscarVendasPeloStatus($status, $idUsuario) {
    }

    public function pagar(Request $requisicao) {
    }
}
