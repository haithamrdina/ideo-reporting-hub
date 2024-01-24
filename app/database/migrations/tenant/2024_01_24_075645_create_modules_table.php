<?php

use App\Enums\CourseStatusEnum;
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
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->string('docebo_id')->unique();
            $table->string('code')->nullable();
            $table->string('name')->nullable();
            $table->string('category')->nullable();
            $table->string('language')->nullable();
            $table->string('article_id')->nullable();
            $table->string('niveau')->nullable();
            $table->json('los')->nullable();
            $table->integer('status')->default(CourseStatusEnum::ACTIVE);
            $table->string('recommended_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};
