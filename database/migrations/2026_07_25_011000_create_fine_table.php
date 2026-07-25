<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('fine')) {
            return;
        }

        Schema::create('fine', function (Blueprint $table) {
            $table->id('FineID');
            $table->decimal('FineAmount', 10, 2);
            $table->string('FineReason', 255)->nullable();
            $table->date('AppliedDate');
            $table->unsignedBigInteger('StudentFeeID');
            // No timestamps: App\Models\Fine declares $timestamps = false.

            $table->foreign('StudentFeeID')->references('StudentFeeID')->on('studentfee');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fine');
    }
};
