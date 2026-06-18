## Why

The CvAnalysisAgent already returns a structured JSON contract, but there is no formal validation layer to guarantee the AI response respects types, ranges, and the recommendation enum before persisting. Malformed or out-of-range AI responses could create corrupted database records. This change introduces a dedicated validation and persistence contract so that every saved analysis is guaranteed typed, cast, and safe.

## What Changes

- Create a FormRequest or dedicated validator for the AI structured JSON response (not user input — AI output validation)
- Validate all 10 fields: types, array contents, `matching_score` range 0–100, `recommandation` enum membership
- Persist the validated analysis into the existing or new database table with Eloquent casts (arrays cast natively, recommendation cast via enum)
- Handle validation failures: reject the analysis, log the error, surface a meaningful French error to the UI without exposing raw AI output
- Ensure the analysis model uses proper casts (array cast for list fields, enum cast for recommandation)
- Update or align with the existing `candidate-submission` flow so the analysis is persisted after the AI job completes

## Capabilities

### New Capabilities
- `structured-analysis-validation`: Validation rules for the 10-field structured JSON contract returned by the AI, covering field types, array contents, integer range, and enum membership
- `analysis-persistence`: Saving validated structured analysis data into the database with Eloquent casts, typed columns, and French enum labels

### Modified Capabilities
- `candidate-submission`: The existing submit flow dispatches an analysis job; the job must now use the new validation and persistence layers instead of saving raw AI output
- `ai-sdk-agent-foundation`: The schema definition in the CvAnalysisAgent remains the source-of-truth for the contract shape; validation requirements are now explicit beyond the SDK schema

## Impact

- **New files**: ValidateStructuredAnalysis action or service class, CandidateAnalysis model with casts, AnalyseRecommandation enum, migration for analysis table columns, tests
- **Modified files**: AnalyseCandidat model (add casts), existing job that calls the AI (add validation step), config (none)
- **Database**: Migration adding `matching_score`, `recommandation`, and array-casted columns if not already present; or a new `candidate_analyses` table
- **AI job**: Must call the validator after receiving the AI response and reject/fail the job if invalid
