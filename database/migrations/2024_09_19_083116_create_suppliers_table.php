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
    Schema::create('suppliers', function (Blueprint $table) {
      $table->bigIncrements('id'); // Primary key
      $table->string('name', 255);
      $table->string('email', 255)->unique();
      $table->string('phone_code')->nullable();
      $table->string('phone', 20)->nullable();
      $table->text('address')->nullable();
      $table->integer('city_id')->nullable();
      $table->integer('country_id')->nullable();
      $table->string('avatar')->nullable();
      $table->text('description')->nullable();
      $table->string('code')->nullable()->unique();
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
    Schema::dropIfExists('suppliers');
  }
};
