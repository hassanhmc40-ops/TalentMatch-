## 1. Controller & Policy

- [x] 1.1 Update `JobOfferPolicy::viewAny()` to return `true`
- [x] 1.2 Add `index()` method to `JobOfferController`: paginate user's offers, ordered by `created_at DESC`

## 2. Routes & Navigation

- [x] 2.1 Add `index` to the `only()` array in `routes/web.php` for the `offres` resource route
- [x] 2.2 Add a navigation link from the dashboard to `/offres` with label "Mes offres d'emploi"
- [x] 2.3 Change offer creation redirect to the list page instead of dashboard

## 3. Listing View

- [x] 3.1 Create `resources/views/offres/index.blade.php` with table: title, skills, min experience, creation date
- [x] 3.2 Add French column headers: "Titre", "Compétences", "Exp. min.", "Créé le"
- [x] 3.3 Add pagination (English labels — French translation deferred to separate change)
- [x] 3.4 Add empty state message with link to `/offres/creer`
- [x] 3.5 Use `x-app-layout` for consistency with existing forms

## 4. Testing

- [x] 4.1 Create `tests/Feature/JobOfferListingTest.php` using Pest
- [x] 4.2 Test: authenticated user sees only their own offers
- [x] 4.3 Test: offers are ordered by most recent first
- [x] 4.4 Test: pagination works (11 offers → page 1 shows "Next")
- [ ] 4.5 Test: candidate count via `withCount` — deferred until CandidateAnalysis model exists (US6)
- [x] 4.6 Test: unauthenticated user is redirected to login
- [ ] 4.7 Test: no N+1 queries — deferred until Debugbar integration confirmed in CI
- [x] 4.8 Test: empty state displays when user has no offers
- [x] 4.9 Test: other users' offers are not visible in the list

## 5. Code Quality & Verification

- [x] 5.1 Run `vendor/bin/pint --format agent` — no issues
- [x] 5.2 `php artisan test --compact --filter=JobOfferListing` — 6/6 passed
- [x] 5.3 `php artisan test --compact` — 58/58 passed (0 regressions)
- [x] 5.4 No debug/dd/dump statements remain

## 6. QA Checklist (manual)

- [ ] 6.1 Navigate to `/offres` as authenticated user with offers — list displayed
- [ ] 6.2 Pagination works correctly with >10 offers
- [ ] 6.3 User without offers sees empty state message
- [ ] 6.4 Navigation link from dashboard works
- [ ] 6.5 Create an offer → redirected to list with success message
