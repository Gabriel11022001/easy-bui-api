<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up() {
        Schema::create('empresas', function (Blueprint $table) {
            $table->id();
            $table->string('razao_social')
                ->nullable(false)
                ->unique('razao_social_unique_key');
            $table->string('cnpj')
                ->nullable(false)
                ->unique('cnpj_unique_id');
            $table->date('data_abertura');
            $table->string('url_site')
                ->nullable(true);
            $table->string('email')
                ->nullable(false)
                ->unique('email_unique_id');
        });
    }

    public function down() {
        Schema::dropIfExists('empresas');
    }
};
