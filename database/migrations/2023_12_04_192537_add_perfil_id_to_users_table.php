<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPerfilIdToUsersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('perfils_id')->nullable(); // Adiciona a coluna 'perfils_id' permitindo nulos
    
            $table->foreign('perfils_id')->references('id')->on('perfils')->onDelete('set null'); // Adiciona a chave estrangeira
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['perfils_id']); // Remove a chave estrangeira
            $table->dropColumn('perfils_id'); // Remove a coluna 'perfil_id'
        });
    }
}
