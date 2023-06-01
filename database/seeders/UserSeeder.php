<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserProfile;
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
            'name' => 'Manuel',
            'email' => 'manuel@trivicare.com',
            'password' => bcrypt('19940520Mog!'),
        ]);
        $cris = User::create([
            'name' => 'Cristina',
            'email' => 'cristina@trivicare.com',
            'password' => bcrypt('Amapola.com27'),
        ]);

        $guest = User::create([
            'name' => 'Guest',
            'email' => 'guest@guest.com',
            'password' => bcrypt('guest'),
        ]);

        $user_profile_guest = UserProfile::create([
            'user_id' => $guest->id,
            'name' => 'Guest',
            'lastname' => 'Guest',
            'address' => 'Guest',
            'optional_address' => 'Guest',
            'city' => 'Guest',
            'state' => 'Guest',
            'country' => 'Guest',
            'zipcode' => 00000,
            'gender' => 'Guest',
            'phone' => 000000000,
        ]);

        $manu->assignRole('admin');
        $cris->assignRole('admin');
        
        User::factory()->count(20)->create();
    }
}
