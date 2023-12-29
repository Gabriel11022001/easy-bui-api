<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up() {
        Schema::create('vendas', function (Blueprint $table) {
            $table->id();
            $table->float('valor_total')
                ->nullable(false);
            $table->dateTime('data_cadastro_venda')
                ->nullable(false);
            $table->date('data_limite_pagamento')
                ->nullable(false);
            $table->string('status')
                ->nullable(false)
                ->default('Aguardando pagamento');
            $table->boolean('parcelado')
                ->nullable(false)
                ->default(false);
            $table->unsignedBigInteger('forma_pagamento_id')
                ->nullable(false);
            $table->unsignedBigInteger('cliente_id')
                ->nullable(false);
            $table->unsignedBigInteger('usuario_id')
                ->nullable(false);
            $table->foreign('cliente_id')
                ->references('id')
                ->on('clientes');
            $table->foreign('usuario_id')
                ->references('id')
                ->on('usuarios');
            $table->foreign('forma_pagamento_id')
                ->references('id')
                ->on('forma_pagamentos');
        });
    }

    public function down() {
        Schema::dropIfExists('vendas');
    }
};
