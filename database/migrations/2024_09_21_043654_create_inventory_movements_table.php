<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('sale_id')->nullable(); // Relasi ke tabel sales
            $table->unsignedBigInteger('purchase_id')->nullable(); // Relasi ke tabel purchases
            $table->enum('type', ['in', 'out']); // in untuk barang masuk (dari pembelian), out untuk barang keluar (dari penjualan)
            $table->integer('quantity');
            $table->timestamp('transaction_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->text('description')->nullable(); // Boleh kosong

            // Relasi foreign key
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('sale_id')->references('id')->on('sales')->onDelete('cascade'); // Relasi ke tabel sales
            $table->foreign('purchase_id')->references('id')->on('purchases')->onDelete('cascade'); // Relasi ke tabel purchases
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
        Schema::dropIfExists('inventory_movements');
    }
};
