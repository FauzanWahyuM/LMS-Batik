<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'personal_phone')) {
                $table->string('personal_phone', 20)->nullable()->after('phone');
            }

            if (!Schema::hasColumn('users', 'forgot_password_enabled')) {
                $table->boolean('forgot_password_enabled')->default(false)->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'forgot_password_enabled')) {
                $table->dropColumn('forgot_password_enabled');
            }

            if (Schema::hasColumn('users', 'personal_phone')) {
                $table->dropColumn('personal_phone');
            }
        });
    }
};
