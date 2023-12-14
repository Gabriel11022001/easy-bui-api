<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up() {
        Schema::create('produtos', function (Blueprint $table) {
            $table->id();
            $table->string('descricao')
                ->nullable(false)
                ->min(1)
                ->max(255);
            $table->string('foto_produto')
                ->nullable(false);
            $table->boolean('status')
                ->nullable(false)
                ->default(true);
            $table->float('preco')
                ->nullable(false)
                ->default(0)
                ->min(0);
            $table->integer('qtd_unidades_estoque')
                ->nullable(false)
                ->min(0)
                ->default(0);
            $table->float('percentual_desconto')
                ->min(0)
                ->max(100)
                ->default(0);
            $table->float('desconto_dinheiro')
                ->min(0)
                ->default(0);
            $table->unsignedBigInteger('categoria_id')
                ->nullable(false);
            $table->unsignedBigInteger('empresa_id')
                ->nullable(false);
            $table->foreign('categoria_id')
                ->references('id')
                ->on('categorias');
            $table->foreign('empresa_id')
                ->references('id')
                ->on('empresas');
        });
    }

    public function down() {
        Schema::dropIfExists('produtos');
    }
};
