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
        Schema::create('recebimentos_sat_banks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('movimentacao_cliente_comercios_id');
            $table->decimal('taxas_clientes', 10, 2);
            $table->decimal('taxas_comercios', 10, 2);
            $table->enum('status', ['ativo', 'recebido', 'estornado'])->default('ativo');
            $table->timestamps();

            $table->foreign('movimentacao_cliente_comercios_id')
                ->references('id')
                ->on('movimentacao_cliente_comercios');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recebimentos_sat_banks');
    }
};
