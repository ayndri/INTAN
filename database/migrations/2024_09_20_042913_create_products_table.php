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

      $table->unsignedBigInteger('category_id')->nullable();
      $table->unsignedBigInteger('brand_id')->nullable();
      $table->unsignedBigInteger('unit_id')->nullable();

      $table->string('item_code', 100)->unique();
      $table->text('description')->nullable();
      $table->string('product_type');
      $table->decimal('sell_price', 15, 2); // Menerima angka hingga 13 digit sebelum koma dan 2 digit setelah koma
      $table->integer('quantity')->default(0);

      $table->integer('quantity_alert');

      // Foreign key constraints (opsional, untuk referensi tabel units dan brands)

      $table->foreign('unit_id')
        ->references('id')->on('units')
        ->onDelete('set null');


      $table->foreign('brand_id')
        ->references('id')->on('brands')
        ->onDelete('set null');

      $table->foreign('category_id')
        ->references('id')->on('categories')
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
