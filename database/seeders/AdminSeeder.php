<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $manu = Admin::create([
            'name' => 'Admin',
            'email' => 'manuel@trivicare.com',
            'password' => bcrypt('19940520Mog!'),
        ]);
        $cris = Admin::create([
            'name' => 'Admin',
            'email' => 'cristina@trivicare.com',
            'password' => bcrypt('Amapola.com27'),
        ]);

        //$manu->assignRole('admin');
        //$cris->assignRole('admin');
    }
}
