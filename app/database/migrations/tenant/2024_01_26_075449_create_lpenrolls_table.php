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
        Schema::create('lpenrolls', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('group_id');
            $table->string('learner_docebo_id');
            $table->string('lp_docebo_id');

            $table->string('status')->nullable();

            $table->string('enrollment_completion_percentage')->nullable();

            $table->string('session_time')->nullable();
            $table->string('cmi_time')->nullable();
            $table->string('calculated_time')->nullable();
            $table->string('recommended_time')->nullable();

            $table->timestamp('enrollment_created_at')->nullable();
            $table->timestamp('enrollment_updated_at')->nullable();
            $table->timestamp('enrollment_completed_at')->nullable();

            $table->foreign('project_id')->references('id')->on('projects');
            $table->foreign('group_id')->references('id')->on('groups');

            $table->foreign('learner_docebo_id')->references('docebo_id')->on('learners');
            $table->foreign('lp_docebo_id')->references('docebo_id')->on('lps');

            $table->unique(['learner_docebo_id', 'lp_docebo_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lpenrolls');
    }
};
