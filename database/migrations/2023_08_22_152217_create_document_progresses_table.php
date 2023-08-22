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
        Schema::create('document_progresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_submission_id')->references('id')->on('document_submissions');
            $table->tinyInteger('status');
            $table->foreignId('user_id')->references('id')->on('users');
            $table->string('file_path')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_progresses');
    }
};
