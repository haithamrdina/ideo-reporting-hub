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
        Schema::create('mooc_project', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mooc_id');
            $table->unsignedBigInteger('project_id');
            $table->timestamps();

            $table->foreign('mooc_id')->references('id')->on('moocs')->onDelete('cascade');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mooc_project');
    }
};
