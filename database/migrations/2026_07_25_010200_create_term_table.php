<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('term')) {
            return;
        }

        Schema::create('term', function (Blueprint $table) {
            $table->id('TermID');
            $table->string('TermName', 50);
            $table->date('StartDate')->nullable();
            $table->date('EndDate')->nullable();
            // No timestamps: App\Models\Term declares $timestamps = false.
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('term');
    }
};
