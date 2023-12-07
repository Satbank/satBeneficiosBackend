<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('admins')->insert([
            'users_id' => 1, // Substitua pelo ID real do usuário associado ao admin
            'nome' => 'Nome do Admin',
            'cnpj' => '123456', // Substitua pelo CNPJ real
            'endereco' => 'Rua Exemplo, 123',
            'telefone' => '123456789',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
