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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('status')->nullable();
            $table->text('subject')->nullable();
            $table->timestamp('ticket_created_at')->nullable();
            $table->timestamp('ticket_updated_at')->nullable();
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('group_id');
            $table->string('learner_docebo_id');
            $table->foreign('project_id')->references('id')->on('projects');
            $table->foreign('group_id')->references('id')->on('groups');
            $table->foreign('learner_docebo_id')->references('docebo_id')->on('learners');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
