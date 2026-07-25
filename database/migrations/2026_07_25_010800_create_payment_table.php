<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('payment')) {
            return;
        }

        Schema::create('payment', function (Blueprint $table) {
            $table->id('PaymentID');
            $table->unsignedBigInteger('StudentFeeID');
            $table->date('PaymentDate');
            $table->decimal('AmountPaid', 10, 2);
            $table->string('PaymentMethod', 30);
            $table->string('TransactionReference', 255)->nullable();
            // No timestamps: App\Models\Payment declares $timestamps = false.

            $table->foreign('StudentFeeID')->references('StudentFeeID')->on('studentfee');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment');
    }
};
