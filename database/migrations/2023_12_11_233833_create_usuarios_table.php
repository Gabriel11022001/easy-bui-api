<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up() {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('nome')
                ->nullable(false);
            $table->string('telefone')
                ->nullable(false)
                ->unique('telefone_unique_id');
            $table->string('email')
                ->nullable(false)
                ->unique('email_unique_id');
            $table->string('senha')
                ->nullable(false)
                ->min(8)
                ->max(25);
            $table->date('data_nascimento')
                ->nullable(false);
            $table->string('sexo')
                ->nullable(false);
            $table->string('cpf')
                ->nullable(false)
                ->min(14)
                ->max(14);
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
            $table->unsignedBigInteger('empresa_id')
                ->nullable(false);
            $table->foreign('empresa_id')
                ->references('id')
                ->on('empresas');
        });
    }

    public function down() {
        Schema::dropIfExists('usuarios');
    }
};
