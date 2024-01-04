<?php

namespace App\Utils;

use App\Models\Produto;

class CalculoUtils
{

    public static function calcularDescontosProdutos($produtos) {

        foreach ($produtos as $prod) {
            $precoOriginal = $prod->preco;
            $percentualDesconto = $prod->percentual_desconto;
            $precoComDesconto = 0;

            if (!$percentualDesconto) {
                $precoComDesconto = $precoOriginal;
            } else {
                $precoComDesconto = $precoOriginal - (($percentualDesconto / 100) * $precoOriginal);
            }

            $prod->preco_com_desconto = $precoComDesconto;
        }

    }

    public static function calcularValorDescontoProduto(&$produto) {
        $precoOriginal = $produto['preco'];   
        $descontoPercentual = $produto['percentual_desconto'];
        $precoComDesconto = 0;

        if (!$descontoPercentual) {
            $precoComDesconto = $precoOriginal;
        } else {
            $precoComDesconto = $precoOriginal - (($descontoPercentual / 100) * $precoOriginal);
        }

        $produto['preco_com_desconto'] = $precoComDesconto;
    }

    public static function calcularValorTotalVenda($items) {
        $valorTotal = 0;
        $descontoTotal = 0;

        foreach ($items as $item) {
            $quantidadeUnidades = $item['qtd_unidades'];
            $produto = Produto::find($item['produto_id']);
            $precoUnitarioProduto = $produto->preco;
            $valorTotal += ($quantidadeUnidades * $precoUnitarioProduto);
            
            if ($produto->desconto_dinheiro != 0) {
                $descontoTotal += $quantidadeUnidades * $produto->desconto_dinheiro;
            }

        }

        return $valorTotal - $descontoTotal;
    }
}