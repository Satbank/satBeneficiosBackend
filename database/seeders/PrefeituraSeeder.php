<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PrefeituraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('prefeituras')->insert([
            'users_id' => 62, // Substitua pelo ID real do usuário associado à prefeitura
            'razao_social' => 'Razão Social da Prefeitura',
            'nome_fantasia' => 'Nome Fantasia da Prefeitura ',
            'cnpj' => '22222222222222', // Substitua pelo CNPJ real
            'telefone' => '11111111111',
            'rua' => 'Rua Exemplo',
            'numero' => '123',
            'bairro' => 'Bairro Exemplo',
            'complemento' => 'Complemento Exemplo',
            'cidade' => 'Cidade Exemplo',
            'uf' => 'UF', // Substitua pela unidade federativa real (por exemplo, SP)
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
