<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('scholarship')) {
            return;
        }

        Schema::create('scholarship', function (Blueprint $table) {
            $table->id('ScholarshipID');
            $table->string('ScholarshipName', 100);
            $table->decimal('DiscountPercentage', 5, 2)->default(0);
            $table->text('Description')->nullable();
            // No timestamps: App\Models\Scholarship declares $timestamps = false.
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scholarship');
    }
};
