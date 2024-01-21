<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

//CreateProductTable
class BuatProdukTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('produk', function (Blueprint $table) {
            $table->increments('id_produk'); //id_product
            $table->unsignedInteger('id_kategori'); //id_category
            $table->string('nama_produk')->unique(); //product name
            $table->string('merk')->nullable(); //brand
            $table->integer('harga_beli'); //purchase price
            $table->tinyInteger('diskon')->default(0); //discount
            $table->integer('wholesale_price')->default(0);
            $table->integer('harga_jual'); //selling price
            $table->integer('stok')->default(0); //stock
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
        Schema::dropIfExists('produk');
    }
}
