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
    Schema::create('purchases', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('supplier_id');
      $table->decimal('tax', 15, 2)->default(0);
      $table->decimal('discount', 15, 2)->default(0);
      $table->decimal('shipping', 15, 2)->default(0);
      $table->decimal('total', 15, 2)->default(0);
      $table->timestamp('purchase_date')->default(DB::raw('CURRENT_TIMESTAMP'));
      $table->string('reference')->unique();
      $table->string('status');
      $table->timestamps();

      // Foreign key
      $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('purchases');
  }
};
