<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJobOfferRequest;
use App\Models\JobOffer;
use Illuminate\Support\Facades\Gate;

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
}
