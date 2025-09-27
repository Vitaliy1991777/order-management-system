<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User; 

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Создаем Руководителя
        User::create([
            'name' => 'Руководитель',
            'email' => 'manager@example.com', 
            'password' => Hash::make('password'),      
            'role' => 'manager',
        ]);

        // Создаем Оператора
        User::create([
            'name' => 'Оператор',
            'email' => 'operator@example.com', 
            'password' => Hash::make('password'),      
            'role' => 'operator',
        ]);
    }
}