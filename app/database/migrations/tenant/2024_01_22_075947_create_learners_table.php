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
        Schema::create('learners', function (Blueprint $table) {
            $table->id();
            $table->string('docebo_id')->unique();
            $table->string('speex_id')->nullable();
            $table->string('lastname')->nullable();
            $table->string('firstname')->nullable();
            $table->string('email')->nullable();
            $table->string('username')->nullable();
            $table->string('last_access_date')->nullable();
            $table->string('creation_date')->nullable();
            $table->string('statut')->nullable();
            $table->string('categorie')->nullable();
            $table->string('cin')->nullable();
            $table->string('matricule')->nullable();
            $table->string('fonction')->nullable();
            $table->string('sexe')->nullable();
            $table->string('direction')->nullable();
            $table->unsignedBigInteger('group_id');
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
            $table->unsignedBigInteger('project_id');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('learners', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
