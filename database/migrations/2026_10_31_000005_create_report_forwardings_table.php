<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_forwardings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->string('pihak', 30);
            $table->text('keterangan')->nullable();
            $table->dateTime('forwarded_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_forwardings');
    }
};