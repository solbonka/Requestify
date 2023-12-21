<?php

namespace Database\Seeders;

use App\Models\Request;
use App\Models\User;
use Illuminate\Database\Seeder;

class RequestSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        Request::truncate();
        $users = User::all();

        foreach ($users as $user) {
            Request::factory()->count(5)->create([
                'user_id' => $user->id,
            ]);
        }
    }
}
