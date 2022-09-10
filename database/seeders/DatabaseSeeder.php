<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Seeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(RoleSeeder::class);

        $this->call(UserSeeder::class);
        UserProfile::factory(21)->create();

        \App\Models\Category::factory(1)->create([
            'name' => 'Facial',
            'slug' => 'facial',
        ]);
        \App\Models\Category::factory(1)->create([
            'name' => 'Corporal',
            'slug' => 'corporal',
        ]);
        \App\Models\Category::factory(1)->create([
            'name' => 'Hidratante',
            'slug' => 'hidratante',
        ]);
        \App\Models\Category::factory(1)->create([
            'name' => 'Accesorios',
            'slug' => 'accesorios',
        ]);

        \App\Models\Product::factory(8)->create();
    }
}
