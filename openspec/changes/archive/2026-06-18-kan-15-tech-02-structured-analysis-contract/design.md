## Context

The app already has:

- `candidate_analyses` table with JSON columns for arrays, integer columns for `years_experience` and `matching_score`, string for `education_level` and `recommendation`, text for `justification`
- `CandidateAnalysis` model with proper Eloquent casts (array, integer, `Recommendation` enum)
- `Recommendation` enum (`convoquer`, `attente`, `rejeter`) with French `label()` method
- `CvAnalysisAgent` with SDK schema using French field keys (`competences_extraites`, `annees_experience`, etc.)
- English column names (`extracted_skills`, `years_experience`, etc.) vs French AI response keys — a mapping layer is needed

Missing: application-level validation of the AI response after the SDK-enforced JSON schema passes, mapping from French response keys to English DB columns, and a clean persistence contract.

## Goals / Non-Goals

**Goals:**
- Define application-level validation rules for the 10-field structured AI JSON response
- Map French AI response keys to English DB column names for persistence
- Persist the validated, mapped data into the `candidate_analyses` table
- Handle validation failures: reject the analysis, log the error, fail the queue job, surface a French error without exposing raw AI output
- Keep the validation layer testable independent of the AI SDK

**Non-Goals:**
- Changing the CvAnalysisAgent schema (it remains the source-of-truth for the AI prompt contract)
- Modifying the SDK's `response_format` configuration
- Creating the queue job or submission flow (already exists or will be covered by candidate-submission tasks)
- User-facing analysis display or UI changes

## Decisions

1. **Service class for validation + mapping** (`ValidateStructuredAnalysis` action class)
   - Why: Keeps validation logic isolated, testable, reusable by the queue job and manual retry
   - Alternative considered: Validator in the job itself → harder to test, violates single responsibility
   - Alternative considered: FormRequest → FormRequests are for HTTP input, not AI output

2. **Static key mapping array** (French→English) in the action class
   - Why: Simple, explicit, no runtime reflection. Both schemas are stable.
   - Alternative considered: Dynamic schema comparison → fragile, over-engineered for 10 fields

3. **Validation approach**: Dedicated rules array not using Laravel Validator
   - Why: AI output validation has different semantics (cannot use `sometimes`, `required_with`, etc.). A clean PHP method chain or match-based check is more readable.
   - Alternative considered: `Illuminate\Validation\Validator` → works but overkill since AI response is already JSON-typed by the SDK; just need range/enum edge checks

4. **Persistence via `CandidateAnalysis::create()`** after validation
   - Why: Model already has proper casts; no need for a separate repository pattern in an MVP
   - Alternative considered: Repository pattern → adds abstraction without proportional benefit at this stage

5. **Validation failure → queue job fails + analysis marked as `failed`**
   - Why: The submission flow is async (queue job); the user must see a clear error state. Adding a `status` column (pending/success/failed) to `candidate_analyses` allows the UI to handle failures gracefully.

## Risks / Trade-offs

- **[Risk] AI returns valid JSON but with values that pass SDK schema yet fail app validation** (e.g., extremely large years_experience like 999) → Mitigation: validate with reasonable business ranges (`years_experience` max 50, `education_level` max length 255)
- **[Risk] Key mapping drifts** if schema is updated but the mapping array is not → Mitigation: the action class lives next to the agent; code review should catch mismatches
- **[Trade-off] Dual validation** (SDK `response_format` + application layer) adds minor overhead but guarantees defense-in-depth for corrupted AI responses
