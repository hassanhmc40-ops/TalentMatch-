## Why

HR agents need to drill into a specific job offer to see its full criteria and the list of candidates that have been analyzed, along with their matching scores and typed recommendations. This is the core decision-making screen where agents decide which candidates to contact.

## What Changes

- Add a `show` route and controller action for viewing a single job offer with its criteria and analyzed candidates
- Create the `CandidateAnalysis` model, migration, and factory with all structured analysis fields
- Create a `Recommendation` enum (`convoquer`, `attente`, `rejeter`) with Eloquent cast support
- Build a French-language offer detail view showing offer criteria and a candidate table with scores and recommendations
- Update `JobOfferPolicy::view()` to authorize offer access by ownership
- Add links from the listing page (`/offres`) to the detail page
- Prevent N+1 queries with eager loading on the detail page

## Capabilities

### New Capabilities
- `job-offer-detail`: Display of a single job offer's criteria and its list of analyzed candidates with matching scores and typed recommendations.

### Modified Capabilities
- (none — detail is a new capability; listing and creation specs remain unchanged)

## Impact

- `app/Models/CandidateAnalysis.php` — new model with relations, casts, fillable
- `app/Enums/Recommendation.php` — new backed enum (convoquer, attente, rejeter)
- `database/migrations/*_create_candidate_analyses_table.php` — new migration
- `database/factories/CandidateAnalysisFactory.php` — new factory
- `app/Http/Controllers/JobOfferController.php` — add `show()` method
- `app/Policies/JobOfferPolicy.php` — update `view()` to check ownership
- `routes/web.php` — add `show` to resource route
- `resources/views/offres/show.blade.php` — new detail view
- `resources/views/offres/index.blade.php` — add links to detail page
- `tests/Feature/JobOfferDetailTest.php` — new test file
