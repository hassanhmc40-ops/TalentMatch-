<?php

namespace App\Models;

use App\Enums\Recommendation;
use Database\Factories\CandidateAnalysisFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    ];

    protected function casts(): array
    {
        return [
            'extracted_skills' => 'array',
            'years_experience' => 'integer',
            'languages' => 'array',
            'matching_score' => 'integer',
            'strengths' => 'array',
            'gaps' => 'array',
            'missing_skills' => 'array',
            'recommendation' => Recommendation::class,
        ];
    }

    public function jobOffer(): BelongsTo
    {
        return $this->belongsTo(JobOffer::class);
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }
}
