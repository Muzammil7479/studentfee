<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('student')) {
            return;
        }

        Schema::create('student', function (Blueprint $table) {
            $table->id('StudentID');
            $table->string('First_Name', 50);
            $table->string('Middle_Name', 50)->nullable();
            $table->string('Last_Name', 50);
            $table->string('Gender', 10)->nullable();
            $table->date('Date_of_Birth')->nullable();
            $table->string('Contact_No', 20)->nullable();
            $table->text('Address')->nullable();
            $table->date('Admission_Date')->nullable();
            $table->unsignedBigInteger('ClassID');
            $table->unsignedBigInteger('SectionID');
            $table->unsignedBigInteger('ParentID');
            $table->unsignedBigInteger('ScholarshipID')->nullable();
            // No timestamps: App\Models\Student declares $timestamps = false.
            // AdminStudentController uses insertGetId(), which requires an
            // auto-incrementing primary key — this matches StudentID here.

            $table->foreign('ClassID')->references('ClassID')->on('class');
            $table->foreign('SectionID')->references('SectionID')->on('section');
            $table->foreign('ParentID')->references('ParentID')->on('parent');
            $table->foreign('ScholarshipID')->references('ScholarshipID')->on('scholarship')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student');
    }
};
