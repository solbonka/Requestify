<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        // Очищаем таблицу пользователей перед заполнением
        User::truncate();

        // Создаем тестовых пользователей

        User::factory()
            ->count(10)
            ->create([
                'password' => Hash::make('password'),
            ]);
    }
}
