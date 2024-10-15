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
        Schema::create('accounting_entries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('description');
            $table->decimal('amount', 15, 2);
            $table->enum('type', ['income', 'expense']);
            $table->timestamp('entry_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->unsignedBigInteger('sale_id')->nullable(); // Relasi dengan sales untuk pendapatan
            $table->unsignedBigInteger('purchase_id')->nullable(); // Relasi dengan purchases untuk pengeluaran

            // Foreign key
            $table->foreign('sale_id')->references('id')->on('sales')->onDelete('cascade'); // Relasi ke sales
            $table->foreign('purchase_id')->references('id')->on('purchases')->onDelete('cascade'); // Relasi ke purchases

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
        Schema::dropIfExists('accounting_entries');
    }
};
