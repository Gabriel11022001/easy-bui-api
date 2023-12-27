<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up() {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nome')
                ->nullable(false)
                ->min(3)
                ->max(255);
            $table->string('cpf')
                ->nullable(false)
                ->min(14)
                ->max(14);
            $table->string('rg')
                ->nullable(false);
            $table->boolean('status')
                ->nullable(false)
                ->default(true);
            $table->date('data_nascimento')
                ->nullable(false);
            $table->string('sexo')
                ->nullable(false)
                ->max(255);
            $table->string('email')
                ->nullable(true)
                ->max(255);
            $table->string('telefone')
                ->nullable(true)
                ->max(255);
                $table->string('cep')
                ->nullable(false);
            $table->text('endereco')
                ->nullable(false);
            $table->string('bairro')
                ->nullable(false);
            $table->string('cidade')
                ->nullable(false);
            $table->string('uf')
                ->nullable(false)
                ->min(2)
                ->max(2);
            $table->string('numero')
                ->nullable(false)
                ->default('s/n');
            $table->unsignedBigInteger('usuario_id')
                ->nullable(false);
            $table->foreign('usuario_id')
                ->references('id')
                ->on('usuarios');
        });
    }

    public function down() {
        Schema::dropIfExists('clientes');
    }
};
