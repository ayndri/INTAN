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
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 255);
            $table->string('sku', 100)->unique();
            $table->decimal('price', 15, 2); // Menerima angka hingga 13 digit sebelum koma dan 2 digit setelah koma
            $table->decimal('cost', 15, 2);  // Sama untuk kolom cost
            $table->integer('stock')->default(0);

            $table->unsignedBigInteger('brand_id')->nullable();
            $table->unsignedBigInteger('unit_id')->nullable();

            $table->boolean('status')->default(true);
            $table->string('product_image')->nullable();

            // Foreign key constraints (opsional, untuk referensi tabel units dan brands)

            $table->foreign('unit_id')
                ->references('id')->on('units')
                ->onDelete('set null');


            $table->foreign('brand_id')
                ->references('id')->on('brands')
                ->onDelete('set null');

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
        Schema::dropIfExists('products');
    }
};
