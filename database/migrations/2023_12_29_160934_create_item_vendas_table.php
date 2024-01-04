<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up() {
        Schema::create('item_vendas', function (Blueprint $table) {
            $table->id();
            $table->float('preco_produto')
                ->nullable(false);
            $table->integer('qtd_unidades')
                ->nullable(false);
            $table->unsignedBigInteger('produto_id')
                ->nullable(false);
            $table->unsignedBigInteger('venda_id')
                ->nullable(false);
            $table->foreign('produto_id')
                ->references('id')
                ->on('produtos');
            $table->foreign('venda_id')
                ->references('id')
                ->on('vendas');
        });
    }

    public function down() {
        Schema::dropIfExists('item_vendas');
    }
};
