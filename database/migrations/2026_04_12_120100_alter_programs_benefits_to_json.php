<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Only modify if the table and column exist
        if (Schema::hasTable('programs') && Schema::hasColumn('programs', 'benefits')) {
            // Get existing data and convert to JSON format
            $programs = DB::table('programs')->get();
            
            foreach ($programs as $program) {
                if ($program->benefits && !str_starts_with($program->benefits, '[')) {
                    // Convert text benefits to JSON array
                    $benefitsArray = array_map('trim', explode(',', $program->benefits));
                    $benefitsArray = array_filter($benefitsArray, fn ($b) => !empty($b));
                    DB::table('programs')
                        ->where('id', $program->id)
                        ->update(['benefits' => json_encode($benefitsArray)]);
                }
            }
            
            // Change column type to JSON
            if (DB::connection()->getDriverName() === 'mysql') {
                DB::statement('ALTER TABLE programs MODIFY benefits JSON DEFAULT (JSON_ARRAY())');
            } elseif (DB::connection()->getDriverName() === 'sqlite') {
                // SQLite doesn't have a JSON type, so we keep it as text but store JSON
                // The Laravel cast will handle the conversion
            } elseif (DB::connection()->getDriverName() === 'pgsql') {
                DB::statement('ALTER TABLE programs ALTER COLUMN benefits TYPE jsonb USING benefits::jsonb');
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('programs') && Schema::hasColumn('programs', 'benefits')) {
            if (DB::connection()->getDriverName() === 'mysql') {
                DB::statement('ALTER TABLE programs MODIFY benefits TEXT');
            } elseif (DB::connection()->getDriverName() === 'pgsql') {
                DB::statement('ALTER TABLE programs ALTER COLUMN benefits TYPE text USING benefits::text');
            }
        }
    }
};
