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

        \App\Models\Tag::factory(1)->create([
            'name' => 'Pomegranate',
            'tag' => 'pomegranate',
            'slug' => 'pomegranate',
            'color' => '#E94B4C',
        ]);
        \App\Models\Tag::factory(1)->create([
            'name' => 'Cannabidiol',
            'tag' => 'cannabidiol',
            'slug' => 'cannabidiol',
            'color' => '#C6D42E',
        ]);
        \App\Models\Tag::factory(1)->create([
            'name' => 'Ácido Hialurónico',
            'tag' => 'acidohialuronico',
            'slug' => 'acido-hialuronico',
            'color' => '#DD88B8',
        ]);
        \App\Models\Tag::factory(1)->create([
            'name' => 'Aloe Vera',
            'tag' => 'aloevera',
            'slug' => 'aloe-vera',
        ]);
        \App\Models\Tag::factory(1)->create([
            'name' => 'Aceite de Granada',
            'tag' => 'aceitedegranada',
            'slug' => 'aceite-de-granada',
        ]);
        \App\Models\Tag::factory(1)->create([
            'name' => 'Viña Roja',
            'tag' => 'viñaroja',
            'slug' => 'vina-roja',
        ]);
        \App\Models\Tag::factory(1)->create([
            'name' => 'Hipérico',
            'tag' => 'hiperico',
            'slug' => 'hiperico',
        ]);
        \App\Models\Tag::factory(1)->create([
            'name' => 'Árnica',
            'tag' => 'arnica',
            'slug' => 'arnica',
        ]);
        \App\Models\Tag::factory(1)->create([
            'name' => 'Extracto de Acai',
            'tag' => 'extractodeacai',
            'slug' => 'extracto-de-acai',
        ]);
        \App\Models\Tag::factory(1)->create([
            'name' => 'Manteca de Karité',
            'tag' => 'mantecadekarite',
            'slug' => 'manteca-de-karite',
        ]);
        \App\Models\Tag::factory(1)->create([
            'name' => 'Vitamina E',
            'tag' => 'vitaminae',
            'slug' => 'vitamina-e',
        ]);
        \App\Models\Tag::factory(1)->create([
            'name' => 'Aceite de Almendras Dulces',
            'tag' => 'aceitedealmendrasdulces',
            'slug' => 'aceite-de-almendras-dulces',
        ]);
        \App\Models\Tag::factory(1)->create([
            'name' => 'Aceite de Jojoba',
            'tag' => 'aceitedejojoba',
            'slug' => 'aceite-de-jojoba',
        ]);
        \App\Models\Tag::factory(1)->create([
            'name' => 'Ginseng',
            'tag' => 'ginseng',
            'slug' => 'ginseg',
        ]);
        \App\Models\Tag::factory(1)->create([
            'name' => 'Ginkgo Biloba',
            'tag' => 'ginkgobiloba',
            'slug' => 'ginkgo-biloba',
        ]);
        \App\Models\Tag::factory(1)->create([
            'name' => 'Caléndula',
            'tag' => 'calendula',
            'slug' => 'calendula',
        ]);
        \App\Models\Tag::factory(1)->create([
            'name' => 'Extracto de Menta',
            'tag' => 'extractodementa',
            'slug' => 'extracto-de-menta',
        ]);
        \App\Models\Tag::factory(1)->create([
            'name' => 'Agua de Azahar',
            'tag' => 'aguadeazahar',
            'slug' => 'agua-de-azahar',
        ]);

        \App\Models\Product::factory(8)->create();

        $this->call(ProductTagSeeder::class);

        \App\Models\Cupon::factory(1)->create([
            'code' => 'CUPON10',
            'discount' => 10,
            'validity' => '2022-10-20',
            'status' => \App\Models\Cupon::ACTIVADO,
        ]);

        \App\Models\Cupon::factory(1)->create([
            'code' => 'CUPON20',
            'discount' => 20,
            'validity' => '2022-10-20',
            'status' => \App\Models\Cupon::ACTIVADO,
        ]);
    }
}
