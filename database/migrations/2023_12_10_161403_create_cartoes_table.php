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
        Schema::create('cartoes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('users_id');
            $table->string('tipo_cartao');
            $table->string('numero_cartao',16);
            $table->decimal('saldo_disponivel', 10, 2)->nullable();
            $table->date('data_emissao');
            $table->string('status');
            $table->date('data_validade');            
            $table->decimal('valor_alocado', 10, 2)->nullable();
            $table->decimal('saldo_atual', 10, 2)->nullable();
            $table->timestamps();
    
            $table->foreign('users_id')->references('id')->on('users');
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cartaos');
    }
};
