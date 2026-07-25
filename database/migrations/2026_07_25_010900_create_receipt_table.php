<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('receipt')) {
            return;
        }

        Schema::create('receipt', function (Blueprint $table) {
            $table->id('ReceiptID');
            // unique() here both enforces "one receipt per payment" (matches
            // App\Models\Payment::receipt() being a hasOne) and gives the
            // foreign key below an index to reference.
            $table->unsignedBigInteger('PaymentID')->unique();
            $table->date('ReceiptDate')->nullable();
            $table->string('ReceiptNumber', 50)->unique();
            // No timestamps: App\Models\Receipt declares $timestamps = false.

            $table->foreign('PaymentID')->references('PaymentID')->on('payment');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('receipt');
    }
};
