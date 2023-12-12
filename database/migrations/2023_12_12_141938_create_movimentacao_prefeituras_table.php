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
        Schema::create('movimentacao_prefeituras', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('prefeituras_id');
            $table->enum('tipo', ['entrada', 'saida']);
            $table->decimal('valor_alocado', 10, 2);
            $table->decimal('saldo', 10, 2);
            $table->timestamps();

            $table->foreign('prefeituras_id')->references('id')->on('prefeituras');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimentacao_prefeituras');
    }
};
