<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("\n            UPDATE modules\n            SET duration = COALESCE(\n                CAST(REGEXP_SUBSTR(duration, '[0-9]+(\\\\.[0-9]+)?') AS DECIMAL(8,2)),\n                1.00\n            )\n        ");

        DB::statement('ALTER TABLE modules MODIFY COLUMN duration DECIMAL(8,2) NOT NULL DEFAULT 1.00');
    }

    public function down(): void
    {
        DB::statement("\n            UPDATE modules\n            SET duration = CONCAT(CAST(duration AS CHAR), ' jam')\n        ");

        DB::statement('ALTER TABLE modules MODIFY COLUMN duration VARCHAR(100) NOT NULL');
    }
};
