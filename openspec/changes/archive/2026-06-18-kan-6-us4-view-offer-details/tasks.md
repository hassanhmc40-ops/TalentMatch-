## 1. Database & Enum

- [x] 1.1 Create `app/Enums/Recommendation.php` backed string enum with `label()` method
- [x] 1.2 Run `php artisan make:migration create_candidate_analyses_table` with all structured output fields
- [x] 1.3 Run `php artisan migrate`

## 2. Model & Factory

- [x] 2.1 Run `php artisan make:model CandidateAnalysis` with fillable, casts (enum + array), belongsTo relations
- [x] 2.2 Create `database/factories/CandidateAnalysisFactory.php` with fake French data

## 3. Controller, Policy & Routes

- [x] 3.1 Update `JobOfferPolicy::view()` to check ownership (`$user->id === $jobOffer->user_id`)
- [x] 3.2 Add `show(JobOffer $offre)` with `Gate::authorize('view', $offre)` + eager loading
- [x] 3.3 Add `show` to `only()` array + register policy via `#[UsePolicy]` attribute on JobOffer

## 4. Detail View

- [x] 4.1 Create `resources/views/offres/show.blade.php` with offer criteria in French
- [x] 4.2 Add candidate analysis table with "Candidat", "Score", "Recommandation"
- [x] 4.3 French recommendation display with colored badges (green/yellow/red)
- [x] 4.4 Empty state: "Aucun candidat analysé pour cette offre."
- [x] 4.5 Link from index view title to detail page

## 5. Testing

- [x] 5.1 Create `tests/Feature/JobOfferDetailTest.php`
- [x] 5.2 Owner sees offer criteria
- [x] 5.3 Unauthenticated → login
- [x] 5.4 Non-owner → 403
- [x] 5.5 Non-existent → 404
- [x] 5.6 Candidates table with scores and recommendations
- [x] 5.7 Empty state
- [x] 5.8 Enum label returns French
- [x] 5.9 Model stores and casts correctly

## 6. Code Quality & Verification

- [x] 6.1 `vendor/bin/pint --format agent` — fixed imports
- [x] 6.2 `php artisan test --compact --filter=JobOfferDetail` — 8/8 passed
- [x] 6.3 `php artisan test --compact` — 66/66 passed (0 regressions)
- [x] 6.4 No debug/dd/dump statements remain

## 7. QA Checklist (manual)

- [ ] 7.1 Navigate to an offer detail page as the owner — criteria displayed
- [ ] 7.2 Candidate table shows analyzed candidates with scores and recommendations
- [ ] 7.3 French recommendation labels display correctly
- [ ] 7.4 Non-owner receives 403
- [ ] 7.5 Non-existent offer returns 404
- [ ] 7.6 Link from listing page to detail works
- [ ] 7.7 Debugbar shows no N+1 queries
