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
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('users_id');
            $table->unsignedBigInteger('prefeitura_id');
            $table->string('nome');
            $table->string('cpf')->unique();         
            $table->string('telefone', 15);
            $table->string('rua',60)->nullable();
            $table->string('numero',10)->nullable();
            $table->string('bairro',50)->nullable();
            $table->string('complemento',200)->nullable();
            $table->string('cidade',60)->nullable();
            $table->string('uf', 2)->nullable();
            $table->timestamps();


                    // Adiciona as chaves estrangeiras
          $table->foreign('prefeitura_id')->references('id')->on('prefeituras');
          $table->foreign('users_id')->references('id')->on('users');
        });
    }

  

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
