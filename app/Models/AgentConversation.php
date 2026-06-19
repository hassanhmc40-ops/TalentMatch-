<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AgentConversation extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'user_id',
        'title',
        'candidate_analysis_id',
    ];

    public function getTable(): string
    {
        return config('ai.conversations.tables.conversations', 'agent_conversations');
    }

    public function candidateAnalysis(): BelongsTo
    {
        return $this->belongsTo(CandidateAnalysis::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(AgentConversationMessage::class, 'conversation_id', 'id')->orderBy('created_at');
    }

    public function latestMessage(): HasOne
    {
        return $this->hasOne(AgentConversationMessage::class, 'conversation_id', 'id')->latestOfMany('created_at');
    }

    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeWithCandidateAnalysis($query)
    {
        return $query->whereNotNull('candidate_analysis_id')
            ->with([
                'candidateAnalysis.candidate',
                'candidateAnalysis.jobOffer',
                'latestMessage',
            ]);
    }

    public function scopeForDashboard($query, int $userId)
    {
        return $query->byUser($userId)
            ->whereNotNull('candidate_analysis_id')
            ->with([
                'candidateAnalysis.candidate',
                'candidateAnalysis.jobOffer',
                'latestMessage',
            ])
            ->withCount('messages')
            ->orderByDesc('updated_at')
            ->take(10);
    }
}
