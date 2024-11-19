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
    Schema::create('variant_products', function (Blueprint $table) {
      $table->id();

      $table->unsignedBigInteger('variant_id')->nullable();
      $table->unsignedBigInteger('product_id')->nullable();
      $table->string('value');
      $table->float('price');
      $table->integer('quantity');

      $table->foreign('variant_id')
        ->references('id')->on('variants')
        ->onDelete('set null');

      $table->foreign('product_id')
        ->references('id')->on('products')
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
    Schema::dropIfExists('variant_products');
  }
};
