<?php

namespace App\Models;

use Database\Factories\JobOfferFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobOffer extends Model
{
    /** @use HasFactory<JobOfferFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'required_skills',
        'min_experience_years',
    ];

    protected function casts(): array
    {
        return [
            'required_skills' => 'array',
            'min_experience_years' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function candidateAnalyses(): HasMany
    {
        return $this->hasMany(CandidateAnalysis::class);
    }
}
