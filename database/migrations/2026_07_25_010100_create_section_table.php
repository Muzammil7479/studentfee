<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('section')) {
            return;
        }

        Schema::create('section', function (Blueprint $table) {
            $table->id('SectionID');
            $table->string('SectionName', 50);
            $table->unsignedBigInteger('ClassID');
            // No timestamps: App\Models\Section declares $timestamps = false.

            $table->foreign('ClassID')->references('ClassID')->on('class');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('section');
    }
};
