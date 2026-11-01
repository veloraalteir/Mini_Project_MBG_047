<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->foreignId('school_id')->constrained()->restrictOnDelete();
            $table->foreignId('distribution_id')->constrained()->restrictOnDelete();
            $table->string('judul', 150);
            $table->text('deskripsi');
            $table->dateTime('waktu_kejadian');
            $table->unsignedInteger('jumlah_terdampak');
            $table->string('status', 30)->default('diajukan')->index();
            $table->text('catatan_pemantau')->nullable();
            $table->string('evidence_path')->nullable();
            $table->string('evidence_name')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};