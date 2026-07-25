<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Recreates the `class` table used by App\Models\SchoolClass.
 *
 * This table (and the other SchoolM core tables added alongside it) existed
 * only as manually imported SQL on the original developer's local MySQL
 * database — there was no Laravel migration for it. That is fine for a
 * database you create by hand in phpMyAdmin, but it means a fresh database
 * (such as the one Railway provisions) never gets these tables, which is
 * why every page except login/dashboard returned HTTP 500 after deploy.
 *
 * Column names intentionally match exactly what the app already queries
 * via DB::table('class') across the controllers and App\Support\SchoolMData,
 * so no business logic changes are required.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('class')) {
            return;
        }

        Schema::create('class', function (Blueprint $table) {
            $table->id('ClassID');
            $table->string('ClassName', 50);
            $table->string('AcademicYear', 20)->nullable();
            // No timestamps: App\Models\SchoolClass declares $timestamps = false
            // and the app never writes created_at/updated_at for this table.
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class');
    }
};
