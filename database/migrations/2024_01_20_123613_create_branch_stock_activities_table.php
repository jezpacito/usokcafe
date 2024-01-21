<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBranchStockActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('branch_stock_activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('added_by_user');
            $table->string('name');
            $table->text('description');
            $table->integer('stock_number_before');
            $table->integer('stock_number_after');
            $table->unsignedBigInteger('branch_stock_id');;
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
        Schema::dropIfExists('branch_stock_activities');
    }
}
