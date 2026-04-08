<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('forum_discussion_replies', function (Blueprint $table) {
            if (!Schema::hasColumn('forum_discussion_replies', 'parent_id')) {
                $table->foreignId('parent_id')
                    ->nullable()
                    ->after('discussion_id')
                    ->constrained('forum_discussion_replies')
                    ->nullOnDelete();

                $table->index('parent_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('forum_discussion_replies', function (Blueprint $table) {
            if (Schema::hasColumn('forum_discussion_replies', 'parent_id')) {
                $table->dropConstrainedForeignId('parent_id');
            }
        });
    }
};