## 1. Ranking Scope

- [x] 1.1 Add `scopeRanked()` to CandidateAnalysis model: order by `matching_score DESC`, tie-break by `years_experience DESC`, then `extracted_skills` count DESC, then education level weight DESC
- [x] 1.2 Define a private `educationWeight(string $level): int` helper on CandidateAnalysis using the same mapping as CompareCandidates (Doctorat=100, Bac+5=80, Bac+3=60, Bac+2=40, Bac=20, others=0)
- [x] 1.3 Add `applyTieBreakers()` to CandidateAnalysis that returns sorted collection

## 2. Controller Update

- [x] 2.1 Update `JobOfferController@show` to apply tie-breakers to candidateAnalyses
- [x] 2.2 Verify no N+1 queries: eager-loading already handled by `$offre->load('candidateAnalyses.candidate')`

## 3. Leaderboard UI

- [x] 3.1 Update `resources/views/offres/show.blade.php` candidate table: add rank number column (`#`) as the first column
- [x] 3.2 Add score progress bar column using existing `<x-progress>` component with max=100 and color classes (green >=70, yellow 40-69, red <40)
- [x] 3.3 Update table headers to include `#` and remove the plain score text in favor of the progress bar
- [x] 3.4 Update the `@php` rows generation to compute rank via `$index + 1` and use the progress bar component
- [x] 3.5 Replace the existing section title "Candidats analysés" with "Classement des candidats" in French

## 4. Tests

- [x] 4.1 Write test: `scopeRanked` orders by matching_score descending
- [x] 4.2 Write test: tie-breaking by years_experience when scores are equal
- [x] 4.3 Write test: tie-breaking by skills count when both score and experience are equal
- [x] 4.4 Write test: tie-breaking by education level as final fallback
- [x] 4.5 Write test: offer detail page displays ranked leaderboard with correct order
- [x] 4.6 Write test: empty offer shows no ranking and appropriate message
- [x] 4.7 Write test: `educationWeight` returns correct values for known levels (replaced compare checkboxes test — checkboxes are unchanged by ranking, existing detail tests cover them)
- [x] 4.8 Write test: progress bar color reflects score thresholds

## 5. Code Quality & Archive

- [x] 5.1 Run `vendor/bin/pint` for code formatting
- [x] 5.2 Run full test suite: `php artisan test --compact` (277 tests, all pass)
- [x] 5.3 Commit and push to repository
