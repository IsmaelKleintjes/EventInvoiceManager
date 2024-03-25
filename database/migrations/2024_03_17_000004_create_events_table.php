<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->text('title');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->foreignId('customer_id')->constrained();
            $table->dateTime('date_time_of_sending_invoice');
            $table->integer('invoice_interval');
            $table->string('invoice_unit');
            $table->integer('invoice_timing');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
