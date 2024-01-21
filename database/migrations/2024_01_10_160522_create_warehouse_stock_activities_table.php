<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWarehouseStockActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('warehouse_stock_activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('added_by_user');
            $table->string('name');
            $table->text('description');
            $table->integer('stock_number_before');
            $table->integer('stock_number_after');
            $table->unsignedBigInteger('warehousestock_id');
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
        Schema::dropIfExists('warehouse_stock_activities');
    }
}
