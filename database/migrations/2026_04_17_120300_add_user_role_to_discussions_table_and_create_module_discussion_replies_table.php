<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('discussions') && !Schema::hasColumn('discussions', 'user_role')) {
            Schema::table('discussions', function (Blueprint $table): void {
                $table->string('user_role')->default('peserta')->after('user_email');
                $table->index('user_role');
            });
        }

        Schema::create('module_discussion_replies', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('discussion_id')->constrained('discussions')->cascadeOnDelete();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_name');
            $table->string('user_email')->nullable();
            $table->string('user_role')->default('peserta');
            $table->longText('content');
            $table->timestamps();

            $table->index('discussion_id');
            $table->index('user_id');
            $table->index('user_role');
            $table->index('created_at');
        });

        if (Schema::hasTable('forum_discussions')) {
            $moduleDiscussions = DB::table('forum_discussions')
                ->whereNotNull('module_id')
                ->orderBy('id')
                ->get();

            foreach ($moduleDiscussions as $discussion) {
                $existing = DB::table('discussions')->where('id', $discussion->id)->exists();

                if (!$existing) {
                    DB::table('discussions')->insert([
                        'id' => $discussion->id,
                        'module_id' => $discussion->module_id,
                        'user_id' => $discussion->user_id,
                        'user_name' => $discussion->user_name,
                        'user_email' => $discussion->user_email,
                        'user_role' => $discussion->user_role ?? 'peserta',
                        'title' => $discussion->title,
                        'content' => $discussion->content,
                        'is_pinned' => $discussion->is_pinned,
                        'is_closed' => $discussion->is_closed,
                        'created_at' => $discussion->created_at,
                        'updated_at' => $discussion->updated_at,
                    ]);
                }

                $moduleReplies = DB::table('forum_discussion_replies')
                    ->where('discussion_id', $discussion->id)
                    ->orderBy('id')
                    ->get();

                foreach ($moduleReplies as $reply) {
                    $replyExists = DB::table('module_discussion_replies')->where('id', $reply->id)->exists();

                    if (!$replyExists) {
                        DB::table('module_discussion_replies')->insert([
                            'id' => $reply->id,
                            'discussion_id' => $reply->discussion_id,
                            'user_id' => $reply->user_id,
                            'user_name' => $reply->user_name,
                            'user_email' => $reply->user_email,
                            'user_role' => $reply->user_role ?? 'peserta',
                            'content' => $reply->content,
                            'created_at' => $reply->created_at,
                            'updated_at' => $reply->updated_at,
                        ]);
                    }
                }

                DB::table('forum_discussion_replies')
                    ->where('discussion_id', $discussion->id)
                    ->delete();

                DB::table('forum_discussions')
                    ->where('id', $discussion->id)
                    ->delete();
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('module_discussion_replies');

        if (Schema::hasTable('discussions') && Schema::hasColumn('discussions', 'user_role')) {
            Schema::table('discussions', function (Blueprint $table): void {
                $table->dropIndex(['user_role']);
                $table->dropColumn('user_role');
            });
        }
    }
};
