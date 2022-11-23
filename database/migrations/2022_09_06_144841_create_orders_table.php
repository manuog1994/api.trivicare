<?php

use App\Models\Order;
use Whoops\Exception\Formatter;
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
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_profile_id')->constrained()->onDelete('cascade');
            $table->json('products');
            $table->decimal('total', 8, 2);
            $table->string('coupon')->nullable();
            $table->string('order_date');
            $table->enum('paid', [
                Order::PENDIENTE,
                Order::PROCESANDO,
                Order::PAGADO,
            ])->default(Order::PENDIENTE);
            $table->enum('status', [
                Order::RECIBIDO,
                Order::PREPARANDO,
                Order::ENVIADO,
                Order::ENTREGADO,
                Order::CANCELADO,
            ])->default(Order::RECIBIDO);
            $table->decimal('shipping', 4, 2)->nullable();
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
