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
        Schema::create('candidate_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_offer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('candidate_id')->constrained()->cascadeOnDelete();
            $table->json('extracted_skills');
            $table->integer('years_experience');
            $table->string('education_level');
            $table->json('languages');
            $table->integer('matching_score');
            $table->json('strengths');
            $table->json('gaps');
            $table->json('missing_skills');
            $table->string('recommendation');
            $table->text('justification');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidate_analyses');
    }
};
