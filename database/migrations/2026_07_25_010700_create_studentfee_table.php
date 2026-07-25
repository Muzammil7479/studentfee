<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('studentfee')) {
            return;
        }

        Schema::create('studentfee', function (Blueprint $table) {
            $table->id('StudentFeeID');
            $table->unsignedBigInteger('StudentID');
            $table->unsignedBigInteger('FeeStructureID');
            $table->date('DueDate')->nullable();
            $table->decimal('TotalAmount', 10, 2)->default(0);
            $table->decimal('DiscountAmount', 10, 2)->default(0);
            $table->decimal('FineAmount', 10, 2)->default(0);
            $table->decimal('RemainingBalance', 10, 2)->default(0);
            $table->string('Status', 30)->default('Pending');
            // No timestamps: App\Models\StudentFee declares $timestamps = false.

            $table->foreign('StudentID')->references('StudentID')->on('student');
            $table->foreign('FeeStructureID')->references('FeeStructureID')->on('feestructure');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('studentfee');
    }
};
