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
            $table->string('name', 255); // Nama supplier
            $table->string('email', 255)->unique(); // Email supplier, unik
            $table->string('phone', 20)->nullable(); // Nomor telepon supplier, opsional
            $table->text('address')->nullable(); // Alamat supplier, opsional
            $table->boolean('status')->default(true); // Status supplier (aktif atau tidak)
            $table->timestamps(); // Kolom created_at dan updated_at otomatis
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
