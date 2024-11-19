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
    Schema::create('sale_products', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('sale_id');
      $table->unsignedBigInteger('product_id');
      $table->integer('quantity');
      $table->decimal('selling_price', 15, 2); // Selling price of the product at the time of sale
      $table->decimal('total', 15, 2); // Total for this product in the sale

      // Foreign keys
      $table->foreign('sale_id')->references('id')->on('sales')->onDelete('cascade');
      $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');

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
    Schema::dropIfExists('sale_products');
  }
};
