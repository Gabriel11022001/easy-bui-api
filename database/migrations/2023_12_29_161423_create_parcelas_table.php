<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up() {
        Schema::create('parcelas', function (Blueprint $table) {
            $table->id();
            $table->integer('numero_parcela')
                ->nullable(false);
            $table->float('valor')
                ->nullable(false);
            $table->date('data_limite_pagamento')
                ->nullable(false);
            $table->boolean('pago')
                ->nullable(false)
                ->default(false);
            $table->unsignedBigInteger('venda_id')
                ->nullable(false);
            $table->foreign('venda_id')
                ->references('id')
                ->on('vendas');
        });
    }

    public function down() {
        Schema::dropIfExists('parcelas');
    }
};
