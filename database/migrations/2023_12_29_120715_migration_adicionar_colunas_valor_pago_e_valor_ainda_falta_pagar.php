<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up() {
        Schema::table('vendas', function (Blueprint $table) {
            $table->addColumn('float', 'valor_pago', [
                'nullable' => false,
                'default' => 0
            ]);
            $table->addColumn('float', 'valor_ainda_falta_pagar', [
                'nullable' => false
            ]);
        });
    }

    public function down() {
    }
};
