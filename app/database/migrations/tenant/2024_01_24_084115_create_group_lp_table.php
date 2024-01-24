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
        Schema::create('group_lp', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lp_id');
            $table->unsignedBigInteger('group_id');
            $table->timestamps();

            $table->foreign('lp_id')->references('id')->on('lps')->onDelete('cascade');
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_lp');
    }
};
