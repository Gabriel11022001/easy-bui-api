<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up() {
        Schema::create('forma_pagamentos', function (Blueprint $table) {
            $table->id();
            $table->string('descricao')
                ->nullable(false)
                ->max(255)
                ->unique('descricao_unique_id');
            $table->boolean('permite_parcelamento')
                ->nullable(false)
                ->default(false);
            $table->boolean('status')
                ->nullable(false)
                ->default(true);
        });
    }

    public function down() {
        Schema::dropIfExists('forma_pagamentos');
    }
};
