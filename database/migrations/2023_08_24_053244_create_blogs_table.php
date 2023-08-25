<?php

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
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->longText('content')->nullable();
            $table->string('imageUrl')->nullable();
            $table->json('category')->nullable();
            $table->string('supplier')->nullable();
            $table->string('author')->nullable();
            $table->string('date')->nullable();
            $table->string('slug')->unique();
            $table->longText('keywords')->nullable();
            $table->string('metaDescription')->nullable();
            $table->string('metaTitle')->nullable();
            $table->json('tags')->nullable();
            $table->text('minTags')->nullable();
            $table->enum('status', ['Publicado', 'Borrador'])->default('Borrador');
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
        Schema::dropIfExists('blogs');
    }
};
