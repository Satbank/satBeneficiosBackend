<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('movimentacao_cliente_comercios', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cartoes_id');
            $table->unsignedBigInteger('comercios_id');
            $table->decimal('valor', 10, 2);
            $table->decimal('valor_original', 10, 2);
            $table->enum('status', ['ativo', 'pago', 'estornado'])->default('ativo');
            $table->timestamps();
    
            // Chaves estrangeiras
            $table->foreign('cartoes_id')->references('id')->on('cartoes');
            $table->foreign('comercios_id')->references('id')->on('comercios');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimentacao_cliente_comercios');
    }
};
