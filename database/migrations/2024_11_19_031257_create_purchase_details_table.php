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
    Schema::create('purchase_details', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('purchase_id');
      $table->unsignedBigInteger('product_id');
      $table->integer('quantity');
      $table->decimal('purchase_price', 15, 2);
      $table->decimal('subtotal', 15, 2);
      $table->timestamps();

      // Foreign keys
      $table->foreign('purchase_id')->references('id')->on('purchases')->onDelete('cascade');
      $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('purchase_details');
  }
};
