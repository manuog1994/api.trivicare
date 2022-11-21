<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
             'email' => 'manuel@trivicare.com',
            'password' => bcrypt('12345678'),
        ]);

        $user->assignRole('admin');
        
        User::factory()->count(20)->create();
    }
}
