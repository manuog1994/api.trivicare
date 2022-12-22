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
        $manu = User::create([
             'email' => 'manuel@trivicare.com',
            'password' => bcrypt('19940520Mog!'),
        ]);
        $cris = User::create([
             'email' => 'cristina@trivicare.com',
            'password' => bcrypt('Amapola.com27'),
        ]);

        $manu->assignRole('admin');
        $cris->assignRole('admin');
        
        //User::factory()->count(20)->create();
    }
}
