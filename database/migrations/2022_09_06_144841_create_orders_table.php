<?php

use App\Models\Order;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->nullable();
            $table->foreignId('user_profile_id')->constrained()->onDelete('cascade')->nullable();
            $table->json('products')->nullable();
            $table->decimal('subTotal', 8, 2)->nullable();
            $table->decimal('total', 8, 2)->nullable();
            $table->string('coupon')->nullable();
            $table->string('order_date')->nullable();
            $table->enum('paid', [
                Order::PENDIENTE,
                Order::PROCESANDO,
                Order::PAGADO,
                Order::RECHAZADO,
            ])->default(Order::PENDIENTE)->nullable();
            $table->enum('status', [
                Order::RECIBIDO,
                Order::PREPARANDO,
                Order::ENVIADO,
                Order::ENTREGADO,
                Order::CANCELADO,
            ])->default(Order::RECIBIDO)->nullable();
            $table->decimal('shipping', 4, 2)->nullable();
            $table->string('token_id')->nullable();
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
        Schema::dropIfExists('orders');
    }
};
