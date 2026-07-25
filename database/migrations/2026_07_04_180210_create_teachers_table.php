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
        if (Schema::hasTable('teachers')) {
            return;
        }

        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->string('teacher_id')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('gender');
            $table->date('dob')->nullable();
            $table->string('cnic')->unique();
            $table->string('phone');
            $table->string('email')->unique();
            $table->text('address')->nullable();
            $table->string('qualification');
            $table->unsignedInteger('experience')->default(0);
            $table->date('joining_date');
            $table->decimal('salary', 10, 2);
            $table->string('class_id')->nullable();
            $table->string('section_id')->nullable();
            $table->string('subject');
            $table->string('photo')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
