## Why

HR users currently see candidate analysis results only as a score/recommendation summary in the offer detail table, or through the AI chat assistant. There is no dedicated, structured, at-a-glance analysis report page. This makes it hard to quickly evaluate a candidate's full profile, skills, gaps, and justification without reading through a conversation.

## What Changes

- Add a dedicated candidate analysis detail page with a full report layout
- Add a route and controller action for viewing a single analysis
- Display all analysis fields: extracted skills, experience, education, languages, matching score, strengths, gaps, missing skills, recommendation, and justification
- Add visual score indicator (progress bar) for the matching score
- Add visual recommendation badge with color coding per recommendation type
- Add navigation from the offer detail page to the analysis detail page
- Add breadcrumb navigation from analysis view back to offer

## Capabilities

### New Capabilities
- `candidate-analysis-view`: Structured analysis detail page showing the full AI evaluation report with score visualization and typed recommendation display

### Modified Capabilities
- (none — existing spec behavior is unchanged)

## Impact

- `app/Http/Controllers/CandidateAnalysisController.php` — new controller with `show()` method
- `routes/web.php` — new route for analysis detail
- `resources/views/candidate-analyses/show.blade.php` — new analysis report view
- `resources/views/offres/show.blade.php` — add link to analysis detail page per candidate row
- `app/Models/CandidateAnalysis.php` — possibly add computed/accessor helpers for display
- No new database columns or migrations (all data already exists)
