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
        Schema::create('calls', function (Blueprint $table) {
            $table->id();
            $table->string('type')->nullable();
            $table->string('status')->nullable();
            $table->text('subject')->nullable();
            $table->timestamp('date_call');
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('group_id');
            $table->string('learner_docebo_id');
            $table->foreign('project_id')->references('id')->on('projects');
            $table->foreign('group_id')->references('id')->on('groups');
            $table->foreign('learner_docebo_id')->references('docebo_id')->on('learners');
            $table->unique(['learner_docebo_id', 'date_call']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calls');
    }
};
