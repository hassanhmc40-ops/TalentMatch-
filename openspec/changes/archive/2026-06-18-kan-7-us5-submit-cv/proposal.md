## Why

KAN-7 US5 is the core user story enabling HR agents to submit a candidate and their CV text against a job offer. This is where the entire AI analysis pipeline begins — the submission form is the entry point for all downstream AI work. The backend infrastructure (validation, queue job, persistence) already exists across prior specs; this change formalizes the complete end-to-end user story including the submission form route, controller integration, and French-language UX feedback.

## What Changes

- Add a `POST /offres/{offre}/candidats` route (already exists) with a submission form view
- The form accepts candidate name (nom) and CV text (cv_text)
- On submit: validate via `SubmitCandidateRequest`, create `Candidate` + pending `CandidateAnalysis`, dispatch `AnalyseCvJob`, redirect with French success message
- Handle errors: validation errors display inline in French, duplicate detection shows French error, unauthorized users get 403

## Capabilities

### New Capabilities
- `submit-cv-ui`: The Blade submission form view for submitting a candidate against a job offer

### Modified Capabilities
- `candidate-submission`: Add requirements for the UI form flow, authorization gating, and end-to-end submission response

## Impact

- **New files**: `resources/views/offres/submit-candidate.blade.php` submission form view
- **Modified files**: `app/Http/Controllers/JobOfferController.php` (already has `submitCandidate`), `routes/web.php` (already has route)
- **Tests**: Existing feature tests already cover submission; may add view assertion tests
