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
        Schema::table('registration_individuals', function (Blueprint $table): void {
            $table->dropUnique('registration_individuals_email_unique');
        });

        Schema::table('registration_groups', function (Blueprint $table): void {
            $table->dropUnique('registration_groups_email_pic_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registration_individuals', function (Blueprint $table): void {
            $table->unique('email');
        });

        Schema::table('registration_groups', function (Blueprint $table): void {
            $table->unique('email_pic');
        });
    }
};
