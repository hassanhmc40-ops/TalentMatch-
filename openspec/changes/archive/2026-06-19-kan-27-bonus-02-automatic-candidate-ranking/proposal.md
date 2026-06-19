## Why

HR agents currently see candidates in arbitrary order on the offer detail page. When multiple candidates have been analysed for the same offer, there is no way to quickly identify who the top candidates are. A ranked leaderboard view solves this — candidates are ordered by matching score with clear visual indicators so the best matches surface first.

## What Changes

- Add a ranked query scope on CandidateAnalysis that orders by matching_score descending with deterministic tie-breakers (years_experience, skills count, education level)
- Replace the candidate table on the offer detail page with a ranked leaderboard view
- Add a dedicated leaderboard section with rank number, score progress bar, recommendation badge, and key stats per candidate
- Add a `scopeRanked()` method and a `rank` accessor on CandidateAnalysis for reuse
- Update the existing candidate comparison flow to work from ranked context (optional)

## Capabilities

### New Capabilities
- `candidate-ranking`: Ranking query scope with tie-breaking, leaderboard UI component, and ranked display on the offer detail page

### Modified Capabilities
- `job-offer-detail`: The candidate list section SHALL be ordered by matching score descending with rank indicators instead of arbitrary collection order

## Impact

- `app/Models/CandidateAnalysis.php`: Add `scopeRanked()` and `rank` accessor
- `app/Http/Controllers/JobOfferController.php`: Update `show()` to use ranked scope
- `resources/views/offres/show.blade.php`: Restructure candidate list section into ranked leaderboard
- New Blade components if needed (leaderboard card/row)
- No new routes required — ranking is integrated into existing offer detail view
- No new database schema changes — all data already exists
