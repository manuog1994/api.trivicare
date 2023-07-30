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
        Schema::table('variations', function (Blueprint $table) {
            $table->string('model')->nullable()->change();
            $table->string('color')->nullable()->change();
            $table->string('size')->nullable()->change();
        });
    }
    
    public function down()
    {
        Schema::table('variations', function (Blueprint $table) {
            $table->string('model')->nullable(false)->change();
            $table->string('color')->nullable(false)->change();
            $table->string('size')->nullable(false)->change();
        });
    }
};
