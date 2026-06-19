<?php

namespace App\Models;

use App\Enums\Recommendation;
use Database\Factories\CandidateAnalysisFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

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

    public function scopeRanked(Builder $query): Builder
    {
        return $query->orderByDesc('matching_score');
    }

    public static function applyTieBreakers(Collection $analyses): Collection
    {
        return $analyses->sort(function (self $a, self $b) {
            if ($a->matching_score !== $b->matching_score) {
                return $b->matching_score <=> $a->matching_score;
            }

            if ($a->years_experience !== $b->years_experience) {
                return $b->years_experience <=> $a->years_experience;
            }

            $skillsA = count($a->extracted_skills ?? []);
            $skillsB = count($b->extracted_skills ?? []);

            if ($skillsA !== $skillsB) {
                return $skillsB <=> $skillsA;
            }

            return static::educationWeight($b->education_level ?? '') <=> static::educationWeight($a->education_level ?? '');
        })->values();
    }

    public static function educationWeight(?string $level): int
    {
        $level = strtolower(trim($level ?? ''));

        $map = [
            'doctorat' => 100,
            'bac+5' => 80,
            'bac+3' => 60,
            'bac+2' => 40,
            'bac' => 20,
            'bts' => 40,
            'dut' => 40,
            'licence' => 60,
            'master' => 80,
            'master 2' => 80,
            'master 1' => 60,
            'ingénieur' => 80,
            'phd' => 100,
        ];

        foreach ($map as $key => $score) {
            if (str_contains($level, $key)) {
                return $score;
            }
        }

        return 0;
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
