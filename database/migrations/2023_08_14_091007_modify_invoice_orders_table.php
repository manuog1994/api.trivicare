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
        Schema::table('invoice_orders', function (Blueprint $table) {
            $table->foreignId('order_id')->nullable()->change();
            $table->foreignId('user_profile_id')->nullable()->change();
            $table->integer('order_seller_id')->after('user_profile_id')->nullable();
            $table->integer('seller_id')->after('order_seller_id')->nullable();
            $table->string('name')->after('invoice_number')->nullable();
            $table->string('lastname')->after('name')->nullable();
            $table->string('email')->after('lastname')->nullable();
            $table->string('phone')->after('email')->nullable();
            $table->text('address')->after('phone')->nullable();
            $table->string('city')->after('address')->nullable();
            $table->string('state')->after('city')->nullable();
            $table->string('zipcode')->after('state')->nullable();
            $table->string('country')->after('zipcode')->nullable();
            $table->string('dni')->after('country')->nullable();
            $table->decimal('total', 8, 2)->after('dni')->nullable()->default(0);
            $table->enum('type', ['Particular', 'Comercial', 'Tienda'])->after('total')->default('Particular');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoice_orders', function (Blueprint $table) {
            $table->dropColumn('order_seller_id');
            $table->dropColumn('seller_id');
            $table->dropColumn('name');
            $table->dropColumn('lastname');
            $table->dropColumn('total');
            $table->dropColumn('type');
            $table->dropColumn('email');
            $table->dropColumn('phone');
            $table->dropColumn('address');
            $table->dropColumn('city');
            $table->dropColumn('state');
            $table->dropColumn('zipcode');
            $table->dropColumn('country');
            $table->dropColumn('dni');
        });
    }
};
