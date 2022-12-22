<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Schema table name to migrate
     * @var string
     */
    public $set_schema_table = 'bt_user_level_attempt';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE orders MODIFY COLUMN paid ENUM("PENDIENTE", "PROCESANDO", "PAGADO", "RECHAZADO", "CONTRAREEMBOLSO", "TRANSFERENCIA") NOT NULL DEFAULT "PENDIENTE"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE orders MODIFY COLUMN paid ENUM("PENDIENTE", "PROCESANDO", "PAGADO", "RECHAZADO") NOT NULL DEFAULT "PENDIENTE"');
    }
};
