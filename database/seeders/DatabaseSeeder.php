<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Tag;
use App\Models\User;
use App\Models\Product;
use App\Models\UserProfile;
use Illuminate\Database\Seeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
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

        \App\Models\Tag::factory(1)->create([
            'name' => 'pomegranate',
            'slug' => 'pomegranate',
            'color' => '#E94B4C',
        ]);
        \App\Models\Tag::factory(1)->create([
            'name' => 'cannabidiol',
            'slug' => 'cannabidiol',
            'color' => '#C6D42E',
        ]);
        \App\Models\Tag::factory(1)->create([
            'name' => 'acidohialuronico',
            'slug' => 'acido-hialuronico',
            'color' => '#DD88B8',
        ]);
        \App\Models\Tag::factory(1)->create([
            'name' => 'aloevera',
            'slug' => 'aloe-vera',
        ]);
        \App\Models\Tag::factory(1)->create([
            'name' => 'aceitedegranada',
            'slug' => 'aceite-de-granada',
        ]);
        \App\Models\Tag::factory(1)->create([
            'name' => 'viñaroja',
            'slug' => 'vina-roja',
        ]);
        \App\Models\Tag::factory(1)->create([
            'name' => 'hiperico',
            'slug' => 'hiperico',
        ]);
        \App\Models\Tag::factory(1)->create([
            'name' => 'arnica',
            'slug' => 'arnica',
        ]);
        \App\Models\Tag::factory(1)->create([
            'name' => 'extractodeacai',
            'slug' => 'extracto-de-acai',
        ]);
        \App\Models\Tag::factory(1)->create([
            'name' => 'mantecadekarite',
            'slug' => 'manteca-de-karite',
        ]);
        \App\Models\Tag::factory(1)->create([
            'name' => 'vitaminae',
            'slug' => 'vitamina-e',
        ]);
        \App\Models\Tag::factory(1)->create([
            'name' => 'aceitedealmendrasdulces',
            'slug' => 'aceite-de-almendras-dulces',
        ]);
        \App\Models\Tag::factory(1)->create([
            'name' => 'aceitedejojoba',
            'slug' => 'aceite-de-jojoba',
        ]);
        \App\Models\Tag::factory(1)->create([
            'name' => 'ginseng',
            'slug' => 'ginseg',
        ]);
        \App\Models\Tag::factory(1)->create([
            'name' => 'ginkgobiloba',
            'slug' => 'ginkgo-biloba',
        ]);
        \App\Models\Tag::factory(1)->create([
            'name' => 'calendula',
            'slug' => 'calendula',
        ]);
        \App\Models\Tag::factory(1)->create([
            'name' => 'extractodementa',
            'slug' => 'extracto-de-menta',
        ]);
        \App\Models\Tag::factory(1)->create([
            'name' => 'aguadeazahar',
            'slug' => 'agua-de-azahar',
        ]);

        \App\Models\Product::factory(8)->create();

        $this->call(ProductTagSeeder::class);
    }
}
