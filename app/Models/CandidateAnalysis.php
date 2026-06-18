<?php

namespace App\Models;

use App\Enums\Recommendation;
use Database\Factories\CandidateAnalysisFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property array $extracted_skills
 * @property int $years_experience
 * @property string $education_level
 * @property array $languages
 * @property int $matching_score
 * @property array $strengths
 * @property array $gaps
 * @property array $missing_skills
 * @property Recommendation $recommendation
 * @property string $justification
 * @property string $status
 * @property int $job_offer_id
 * @property int $candidate_id
 */
class CandidateAnalysis extends Model
{
    /** @use HasFactory<CandidateAnalysisFactory> */
    use HasFactory;

    protected $fillable = [
        'job_offer_id',
        'candidate_id',
        'extracted_skills',
        'years_experience',
        'education_level',
        'languages',
        'matching_score',
        'strengths',
        'gaps',
        'missing_skills',
        'recommendation',
        'justification',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'extracted_skills' => 'array',
            'years_experience' => 'integer',
            'education_level' => 'string',
            'languages' => 'array',
            'matching_score' => 'integer',
            'strengths' => 'array',
            'gaps' => 'array',
            'missing_skills' => 'array',
            'recommendation' => Recommendation::class,
            'status' => 'string',
        ];
    }

    public function scoreLevel(): string
    {
        return match (true) {
            $this->matching_score >= 81 => 'Excellent',
            $this->matching_score >= 61 => 'Bon',
            $this->matching_score >= 31 => 'Moyen',
            default => 'Faible',
        };
    }

    public function isRecommended(): bool
    {
        return $this->recommendation === Recommendation::Convoquer;
    }

    public function skillCount(): int
    {
        return count($this->extracted_skills ?? []);
    }

    public function missingSkillCount(): int
    {
        return count($this->missing_skills ?? []);
    }

    public function jobOffer(): BelongsTo
    {
        return $this->belongsTo(JobOffer::class);
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(AgentConversation::class, 'candidate_analysis_id');
    }
}
