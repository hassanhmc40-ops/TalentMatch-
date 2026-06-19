## 1. Route and Controller

- [x] 1.1 Add `GET /offres/{offre}/comparer` route with query params `candidats[]`
- [x] 1.2 Add `compare()` method on `JobOfferController` with scoped query for two analyses
- [x] 1.3 Add request validation for candidate IDs (require 2, must belong to offer)

## 2. Offer Detail Page — Candidate Selection

- [x] 2.1 Add Alpine.js selection state to offer detail blade (track selected candidate IDs)
- [x] 2.2 Add checkbox column to candidate table with row highlight on select
- [x] 2.3 Add "Comparer les candidats sélectionnés" button above the table
- [x] 2.4 Wire button to disable unless exactly 2 candidates selected, build comparison URL

## 3. Comparison View Page

- [x] 3.1 Create `resources/views/offres/comparer.blade.php` with two-column Tailwind layout
- [x] 3.2 Add score difference banner at top showing "Écart de score: X points"
- [x] 3.3 Render each candidate's score progress bar with color coding reusing existing components
- [x] 3.4 Render each candidate's strengths (checkmark icons) and gaps (warning icons)
- [x] 3.5 Render recommendation badges using `<x-recommendation-badge>`
- [x] 3.6 Add candidate name links to individual analysis detail pages
- [x] 3.7 Add breadcrumb navigation back to offer detail page
- [x] 3.8 Handle edge cases: pending analysis, failed analysis, candidate not found

## 4. Test Coverage

- [x] 4.1 Write test: comparison page renders both candidates with scores and strengths/gaps
- [x] 4.2 Write test: comparison page returns 403 for unauthorized offer
- [x] 4.3 Write test: comparison page returns 404 for non-existent offer
- [x] 4.4 Write test: redirect with error when less than two candidates provided
- [x] 4.5 Write test: pending/failed analysis shows appropriate status message
- [x] 4.6 Write test: candidate selection appears on offer detail page with correct button state

## 5. Code Quality & Archive

- [x] 5.1 Run `vendor/bin/pint` for code formatting
- [x] 5.2 Run full test suite: `php artisan test --compact`
- [x] 5.3 Commit and push to repository
