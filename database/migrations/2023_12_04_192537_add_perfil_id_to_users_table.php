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
            $table->unsignedBigInteger('perfil_id')->nullable(); // Adiciona a coluna 'perfil_id'

            $table->foreign('perfil_id')->references('id')->on('perfils')->onDelete('set null'); // Adiciona a chave estrangeira
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['perfil_id']); // Remove a chave estrangeira
            $table->dropColumn('perfil_id'); // Remove a coluna 'perfil_id'
        });
    }
}
