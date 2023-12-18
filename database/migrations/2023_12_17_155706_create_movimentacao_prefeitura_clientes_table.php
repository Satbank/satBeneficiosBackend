<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMovimentacaoPrefeituraClientesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('movimentacao_prefeitura_clientes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('users_id')->constrained('users');
            $table->foreignId('prefeituras_id')->constrained('prefeituras');
            $table->enum('tipo', ['entrada', 'saida']);
            $table->decimal('valor_movimentado', 10, 2);
            $table->decimal('valor_movimentado_individual', 10, 2);
            $table->text('descricao')->nullable();
            $table->enum('status', ['pendente', 'concluida', 'cancelada'])->default('pendente');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimentacao_prefeitura_clientes');
    }
}