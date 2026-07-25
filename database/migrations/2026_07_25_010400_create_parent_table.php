<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('parent')) {
            return;
        }

        Schema::create('parent', function (Blueprint $table) {
            $table->id('ParentID');
            $table->string('Father_Name', 100)->nullable();
            $table->string('Mother_Name', 100)->nullable();
            $table->string('Phone_No', 20)->nullable();
            $table->string('Email', 150)->nullable();
            // No timestamps: App\Models\ParentGuardian declares $timestamps = false.
            // AdminStudentController uses insertGetId(), which requires an
            // auto-incrementing primary key — this matches ParentID here.
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parent');
    }
};
