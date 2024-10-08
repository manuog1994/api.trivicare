<?php

use App\Models\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('specifications')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->decimal('price_base', 10, 2)->nullable();
            $table->integer('stock')->nullable();
            $table->bigInteger('barcode')->unique()->nullable();
            $table->foreignId('category_id')->constrained()->onDelete('cascade')->default(1);
            $table->string('slug')->unique()->nullable();
            $table->integer('sold')->nullable();
            $table->enum('status', [Product::BORRADOR, Product::PUBLICADO])->default(Product::BORRADOR)->nullable();
            $table->foreignId('review_id')->nullable();
            $table->integer('discount')->nullable();
            $table->decimal('weight', 10, 2)->nullable();
            $table->decimal('size', 10, 2)->nullable();
            $table->string('dimensions')->nullable();
            $table->integer('rating')->nullable()->default(0);
            $table->integer('total_reviews')->nullable()->default(0);
            $table->decimal('price_discount', 10, 2)->nullable();
            $table->enum('new', [Product::NUEVO, Product::VIEJO])->default(Product::NUEVO)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
};
