## Context

Job offer creation (US2) and listing (US3) are implemented. The next step is a detail/show page for a single offer. The `CandidateAnalysis` model does not exist yet, nor does the `Recommendation` enum. The existing `JobOfferPolicy::view()` returns `false`.

## Goals / Non-Goals

**Goals:**
- Display offer criteria: title, description, required skills, minimum experience
- Display a table of analyzed candidates with: name, matching score, recommendation
- Authorize access: only the offer owner can view details
- Prevent N+1 by eager-loading candidates with their analyses
- Add navigation from the listing page to the detail page

**Non-Goals:**
- Candidate submission (US5) — handled separately
- AI analysis job (US6) — handled separately
- Candidate detail page (US7) — handled separately
- Editing or deleting offers

## Decisions

**1. `CandidateAnalysis` belongs to both `JobOffer` and `Candidate`**
Each analysis links a candidate to a specific job offer, storing the AI's structured evaluation. The migration includes a foreign key to `job_offers` and `candidates`. The `candidate_analyses` table stores all fields from the structured output contract.

**2. `Recommendation` as a backed string enum**
Values: `Convoquer`, `Attente`, `Rejeter`. This enables Eloquent cast (`recommendation` → `Recommendation`) and clean display logic in the view.

**3. Authorization via `JobOfferPolicy::view()`**
Updated to `$user->id === $jobOffer->user_id`. The controller calls `Gate::authorize('view', $offer)` inside `show()`. Consistent with the existing pattern.

**4. Eager loading for N+1 prevention**
The controller loads `$offer->candidateAnalyses()->with('candidate')->get()` to avoid lazy-loading per row.

**5. French display for recommendations**
Enum labels map to French display values: `Convoquer` → `À convoquer`, `Attente` → `En attente`, `Rejeter` → `À rejeter`. Display logic lives in the view or a helper.

## Risks / Trade-offs

- **[Missing AI analysis data]** The detail view shows analyses even if AI hasn't run yet. Initially the candidate table will be empty until US6 is implemented.
- **[Large candidate lists]** If an offer has many candidates, paginate the candidate table. V2 consideration.
