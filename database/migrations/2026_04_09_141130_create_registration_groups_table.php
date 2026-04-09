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
        Schema::create('registration_groups', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lembaga');
            $table->string('alamat_pic');
            $table->string('email_pic')->unique();
            $table->string('no_handphone_pic');
            $table->string('nama_pic');
            $table->integer('jumlah_peserta');
            $table->string('surat_resmi')->nullable(); // file path
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registration_groups');
    }
};
