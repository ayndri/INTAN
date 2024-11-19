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
    Schema::create('sales', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('customer_id');
      $table->decimal('total', 15, 2); // Total sale value
      $table->timestamp('sale_date')->default(DB::raw('CURRENT_TIMESTAMP'));
      $table->string('status')->default('pending');
      $table->string('order_type')->default('offline');
      $table->decimal('shipping_cost', 15, 2)->nullable(); // Shipping cost if any

      // Foreign key for customers
      $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');

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
    Schema::dropIfExists('sales');
  }
};
