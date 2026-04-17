<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('forum_discussions', function (Blueprint $table) {
            $table->string('theme')->nullable()->after('module_id');
            $table->index('theme');
        });

        if (Schema::hasTable('modules')) {
            $moduleTitles = DB::table('modules')
                ->select('id', 'title')
                ->get()
                ->keyBy('id');

            DB::table('forum_discussions')
                ->select('id', 'module_id', 'title')
                ->orderBy('id')
                ->chunkById(200, function ($rows) use ($moduleTitles): void {
                    foreach ($rows as $row) {
                        $theme = (string) ($moduleTitles[$row->module_id]->title ?? $row->title ?? '');
                        $theme = trim($theme);

                        if ($theme === '') {
                            continue;
                        }

                        DB::table('forum_discussions')
                            ->where('id', $row->id)
                            ->update(['theme' => $theme]);
                    }
                });
        } else {
            DB::table('forum_discussions')
                ->where(function ($query) {
                    $query->whereNull('theme')->orWhere('theme', '');
                })
                ->update(['theme' => DB::raw('title')]);
        }
    }

    public function down(): void
    {
        Schema::table('forum_discussions', function (Blueprint $table) {
            $table->dropIndex(['theme']);
            $table->dropColumn('theme');
        });
    }
};
