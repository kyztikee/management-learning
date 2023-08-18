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
        Schema::create('civilians', function (Blueprint $table) {
            $table->foreignId('user_id')->references('id')->on('users');
            $table->primary('user_id');
            $table->string('birth_place');
            $table->date('birth_date');
            $table->tinyInteger('gender');
            $table->string('religion');
            $table->string('nik');
            $table->string('rt');
            $table->string('rw');
            $table->string('phone_no');
            $table->tinyInteger('status')->default(0);
            $table->foreignId('approved_by')->nullable()->references('id')->on('users');
            $table->date('approved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('civilians');
    }
};
