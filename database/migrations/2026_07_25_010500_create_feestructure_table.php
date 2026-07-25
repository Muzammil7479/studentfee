<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * `TotalFee` is never written by the app (AccountController::createClassPlan
 * only ever inserts/updates TuitionFee, ExamFee, TransportFee, MiscFee) yet
 * it is read everywhere (SchoolMData::assignFeeStructureToStudent(),
 * receipts, etc). That only works if TotalFee is a MySQL generated column,
 * so it's recreated here as a STORED GENERATED column that MySQL keeps in
 * sync automatically. This matches the original behavior exactly.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('feestructure')) {
            return;
        }

        Schema::create('feestructure', function (Blueprint $table) {
            $table->id('FeeStructureID');
            $table->unsignedBigInteger('ClassID');
            $table->unsignedBigInteger('TermID');
            $table->decimal('TuitionFee', 10, 2)->default(0);
            $table->decimal('ExamFee', 10, 2)->default(0);
            $table->decimal('TransportFee', 10, 2)->default(0);
            $table->decimal('MiscFee', 10, 2)->default(0);
            $table->decimal('TotalFee', 10, 2)
                ->storedAs('TuitionFee + ExamFee + TransportFee + MiscFee');
            // No timestamps: App\Models\FeeStructure declares $timestamps = false.

            $table->foreign('ClassID')->references('ClassID')->on('class');
            $table->foreign('TermID')->references('TermID')->on('term');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feestructure');
    }
};
