<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJobOfferRequest;
use App\Http\Requests\SubmitCandidateRequest;
use App\Jobs\AnalyseCvJob;
use App\Models\Candidate;
use App\Models\CandidateAnalysis;
use App\Models\JobOffer;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class JobOfferController extends Controller
{
    public function index()
    {
        $offers = JobOffer::query()
            ->where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('offres.index', compact('offers'));
    }

    public function show(JobOffer $offre)
    {
        Gate::authorize('view', $offre);

        $offre->load('candidateAnalyses.candidate');

        return view('offres.show', compact('offre'));
    }

    public function create()
    {
        return view('offres.create');
    }

    public function store(StoreJobOfferRequest $request)
    {
        JobOffer::create([
            'user_id' => $request->user()->id,
            'title' => $request->title,
            'description' => $request->description,
            'required_skills' => $request->required_skills,
            'min_experience_years' => $request->min_experience_years,
        ]);

        return redirect()->route('offres.index')
            ->with('success', "L'offre d'emploi a été créée avec succès.");
    }

    public function createCandidate(JobOffer $offre)
    {
        Gate::authorize('view', $offre);

        return view('offres.submit-candidate', compact('offre'));
    }

    public function submitCandidate(SubmitCandidateRequest $request, JobOffer $offre)
    {
        Gate::authorize('view', $offre);

        $name = Str::lower(trim($request->nom));

        $existingCandidate = Candidate::whereRaw('LOWER(TRIM(name)) = ?', [$name])->first();

        if ($existingCandidate !== null) {
            $existingAnalysis = CandidateAnalysis::query()
                ->where('candidate_id', $existingCandidate->id)
                ->where('job_offer_id', $offre->id)
                ->exists();

            if ($existingAnalysis) {
                return redirect()->route('offres.show', $offre)
                    ->withErrors(['nom' => 'Ce candidat a déjà été soumis pour cette offre.']);
            }
        }

        $candidate = $existingCandidate ?? Candidate::create([
            'name' => $request->nom,
            'cv_text' => $request->cv_text,
        ]);

        CandidateAnalysis::create([
            'job_offer_id' => $offre->id,
            'candidate_id' => $candidate->id,
            'status' => 'pending',
            'extracted_skills' => [],
            'years_experience' => 0,
            'education_level' => '',
            'languages' => [],
            'matching_score' => 0,
            'strengths' => [],
            'gaps' => [],
            'missing_skills' => [],
            'recommendation' => 'attente',
            'justification' => '',
        ]);

        AnalyseCvJob::dispatch($candidate->id, $offre->id);

        return redirect()->route('offres.show', $offre)
            ->with('success', 'Candidature soumise. L\'analyse est en cours.');
    }
}
