<?php

namespace Database\Seeders;

use App\Models\Tag;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProductTagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tag_id = Tag::all()->random()->id;
        $product_id = Product::all()->random()->id;

        DB::table('product_tag')->insert([
            'product_id' => $product_id,
            'tag_id' => $tag_id,
            'name' => Tag::where('id', $tag_id)->first()->name,
            'slug' => Tag::where('id', $tag_id)->first()->slug,
        ]);

        $tag_id = Tag::all()->random()->id;
        $product_id = Product::all()->random()->id;

        DB::table('product_tag')->insert([
            'product_id' => $product_id,
            'tag_id' => $tag_id,
            'name' => Tag::where('id', $tag_id)->first()->name,
            'slug' => Tag::where('id', $tag_id)->first()->slug,
        ]);

        $tag_id = Tag::all()->random()->id;
        $product_id = Product::all()->random()->id;

        DB::table('product_tag')->insert([
            'product_id' => $product_id,
            'tag_id' => $tag_id,
            'name' => Tag::where('id', $tag_id)->first()->name,
            'slug' => Tag::where('id', $tag_id)->first()->slug,
        ]);

        $tag_id = Tag::all()->random()->id;
        $product_id = Product::all()->random()->id;

        DB::table('product_tag')->insert([
            'product_id' => $product_id,
            'tag_id' => $tag_id,
            'name' => Tag::where('id', $tag_id)->first()->name,
            'slug' => Tag::where('id', $tag_id)->first()->slug,
        ]);

        $tag_id = Tag::all()->random()->id;
        $product_id = Product::all()->random()->id;

        DB::table('product_tag')->insert([
            'product_id' => $product_id,
            'tag_id' => $tag_id,
            'name' => Tag::where('id', $tag_id)->first()->name,
            'slug' => Tag::where('id', $tag_id)->first()->slug,
        ]);

        $tag_id = Tag::all()->random()->id;
        $product_id = Product::all()->random()->id;

        DB::table('product_tag')->insert([
            'product_id' => $product_id,
            'tag_id' => $tag_id,
            'name' => Tag::where('id', $tag_id)->first()->name,
            'slug' => Tag::where('id', $tag_id)->first()->slug,
        ]);

        $tag_id = Tag::all()->random()->id;
        $product_id = Product::all()->random()->id;

        DB::table('product_tag')->insert([
            'product_id' => $product_id,
            'tag_id' => $tag_id,
            'name' => Tag::where('id', $tag_id)->first()->name,
            'slug' => Tag::where('id', $tag_id)->first()->slug,
        ]);

        $tag_id = Tag::all()->random()->id;
        $product_id = Product::all()->random()->id;

        DB::table('product_tag')->insert([
            'product_id' => $product_id,
            'tag_id' => $tag_id,
            'name' => Tag::where('id', $tag_id)->first()->name,
            'slug' => Tag::where('id', $tag_id)->first()->slug,
        ]);

        $tag_id = Tag::all()->random()->id;
        $product_id = Product::all()->random()->id;

        DB::table('product_tag')->insert([
            'product_id' => $product_id,
            'tag_id' => $tag_id,
            'name' => Tag::where('id', $tag_id)->first()->name,
            'slug' => Tag::where('id', $tag_id)->first()->slug,
        ]);

        $tag_id = Tag::all()->random()->id;
        $product_id = Product::all()->random()->id;

        DB::table('product_tag')->insert([
            'product_id' => $product_id,
            'tag_id' => $tag_id,
            'name' => Tag::where('id', $tag_id)->first()->name,
            'slug' => Tag::where('id', $tag_id)->first()->slug,
        ]);

    }
}
