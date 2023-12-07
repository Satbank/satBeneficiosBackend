<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       // Inserir dados fictícios
       DB::table('users')->insert([
      
        'email' => 'teste@gmail.com',
        'password' => Hash::make('123456'),
    ]);
    }
}
