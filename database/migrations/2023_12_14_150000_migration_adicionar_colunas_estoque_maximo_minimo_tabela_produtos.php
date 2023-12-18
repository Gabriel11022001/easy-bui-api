<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up() {
        Schema::table('produtos', function (Blueprint $table) {
            $table->addColumn('integer', 'estoque_minimo', [
                'nullable' => true,
                'min' => 0
            ]);
            $table->addColumn('integer', 'estoque_maximo', [
                'nullable' => true,
                'min' => 0                
            ]);
        });
    }

    public function down() {
        Schema::dropColumns('produtos', [ 'estoque_minimo', 'estoque_maximo' ]);
    }
};
