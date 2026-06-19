<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tableName = config('ai.conversations.tables.conversations', 'agent_conversations');

        Schema::table($tableName, function (Blueprint $table) {
            $table->foreignId('candidate_analysis_id')
                ->nullable()
                ->constrained('candidate_analyses')
                ->cascadeOnDelete();

            $table->index(['candidate_analysis_id', 'user_id', 'updated_at'], 'conv_analysis_user_idx');
        });
    }

    public function down(): void
    {
        $tableName = config('ai.conversations.tables.conversations', 'agent_conversations');

        Schema::table($tableName, function (Blueprint $table) {
            $table->dropIndex('conv_analysis_user_idx');
            $table->dropForeign(['candidate_analysis_id']);
            $table->dropColumn('candidate_analysis_id');
        });
    }
};
