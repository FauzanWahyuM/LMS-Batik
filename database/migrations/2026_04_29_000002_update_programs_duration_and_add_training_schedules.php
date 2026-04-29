<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            // Add new duration field as string
            if (!Schema::hasColumn('programs', 'duration')) {
                $table->string('duration', 100)->nullable()->after('name');
            }

            // Add training schedules as JSON
            if (!Schema::hasColumn('programs', 'training_schedules')) {
                $table->json('training_schedules')->default(json_encode([]))->after('benefits');
            }
        });

        // Migrate existing data
        $programs = DB::table('programs')->get();
        foreach ($programs as $program) {
            $durationLabel = '';
            if ($program->duration_hours) {
                $value = (float) $program->duration_hours;
                $hasFraction = abs($value - floor($value)) > 0.00001;
                $decimals = $hasFraction ? 1 : 0;
                $unit = ($program->duration_unit ?? 'hours') === 'minutes' ? 'menit' : 'jam';
                $durationLabel = number_format($value, $decimals, ',', '.') . ' ' . $unit;
            }

            DB::table('programs')
                ->where('id', $program->id)
                ->update(['duration' => $durationLabel]);
        }

        // Drop old columns
        Schema::table('programs', function (Blueprint $table) {
            if (Schema::hasColumn('programs', 'duration_hours')) {
                $table->dropColumn('duration_hours');
            }
            if (Schema::hasColumn('programs', 'duration_unit')) {
                $table->dropColumn('duration_unit');
            }
        });
    }

    public function down(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            // Re-add old columns
            if (!Schema::hasColumn('programs', 'duration_hours')) {
                $table->decimal('duration_hours', 8, 2)->nullable()->after('name');
            }
            if (!Schema::hasColumn('programs', 'duration_unit')) {
                $table->string('duration_unit', 20)->default('hours')->after('duration_hours');
            }
        });

        // Revert duration data if needed
        // This is optional, can be left empty

        // Drop new columns
        Schema::table('programs', function (Blueprint $table) {
            if (Schema::hasColumn('programs', 'duration')) {
                $table->dropColumn('duration');
            }
            if (Schema::hasColumn('programs', 'training_schedules')) {
                $table->dropColumn('training_schedules');
            }
        });
    }
};
