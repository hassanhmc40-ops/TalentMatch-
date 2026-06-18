## 1. Database & Migration

- [x] 1.1 Create migration to add `status` column (pending/completed/failed) with default `pending` to `candidate_analyses` table
- [x] 1.2 Add `status` to `CandidateAnalysis` model `$fillable` and cast it to a string or dedicated enum

## 2. Validation Layer

- [x] 2.1 Create `app/Actions/ValidateStructuredAnalysis.php` action class
- [x] 2.2 Implement static French→English key mapping array (`competences_extraites` → `extracted_skills`, `annees_experience` → `years_experience`, etc.)
- [x] 2.3 Implement `validate(array $data): array` method that validates all 10 fields per the structured-analysis-validation spec: string types, array-of-strings types, integer range 0-100 for matching_score, 0-50 for years_experience, enum membership for recommandation, missing field detection
- [x] 2.4 Throw a typed `ValidationFailedException` (or similar) on failure with details about which field(s) failed
- [x] 2.5 Log validation errors at `error` level with a truncated AI response preview (no raw full response in logs)

## 3. Persistence & Mapping

- [x] 3.1 Create `app/Actions/PersistValidatedAnalysis.php` action class
- [x] 3.2 Implement `persist(array $validatedData, int $jobOfferId, int $candidateId): CandidateAnalysis` method
- [x] 3.3 Apply the English key mapping inside persist (or reuse from validation action)
- [x] 3.4 Use `CandidateAnalysis::create()` with mapped data — Eloquent casts handle array→JSON and string→Enum automatically
- [x] 3.5 Set `status` to `completed` after successful creation
- [x] 3.6 Guard against duplicate analysis for same candidate + offer (unique constraint or application check)

## 4. Queue Job

- [x] 4.1 Create `app/Jobs/AnalyseCvJob.php` that accepts `candidateId` and `jobOfferId`
- [x] 4.2 In `handle()`: call `CvAnalysisAgent`, receive structured response, pass to validate action, then persist action
- [x] 4.3 On validation failure: catch exception, log, set analysis status to `failed`, fail the job
- [x] 4.4 On AI/ApiException: release the job back to the queue with a delay for transient errors
- [x] 4.5 Register queue connection and ensure `.env` has `QUEUE_CONNECTION=database`

## 5. Integration with Submission Flow

- [x] 5.1 Update the existing `SubmitCandidateRequest` flow (or create a controller method) to dispatch `AnalyseCvJob` after creating the `Candidate` record
- [x] 5.2 Set initial analysis record status to `pending` when the submission is accepted
- [x] 5.3 Return a meaningful French response to the user: "Candidature soumise. L'analyse est en cours."

## 6. Tests

- [x] 6.1 Test `ValidateStructuredAnalysis` with valid full payload (all 10 fields present and correct)
- [x] 6.2 Test string validation (empty string, non-string value, max length exceeded)
- [x] 6.3 Test array validation (non-array, array with non-string items, empty array for optional fields)
- [x] 6.4 Test matching_score range (below 0, above 100, non-integer)
- [x] 6.5 Test years_experience range (negative, exceeding 50)
- [x] 6.6 Test recommandation enum (invalid string, missing field)
- [x] 6.7 Test missing required field detection
- [x] 6.8 Test key mapping: French AI keys map to correct English DB columns
- [x] 6.9 Test `PersistValidatedAnalysis` creates a valid `CandidateAnalysis` record with correct casts
- [x] 6.10 Test duplicate analysis guard rejects second analysis for same candidate + offer
- [x] 6.11 Test AnalyseCvJob dispatches and handles success
- [x] 6.12 Test AnalyseCvJob handles validation failure (status = failed, no record created)
- [x] 6.13 Test French error messages are returned for submission validation
- [x] 6.14 Run `vendor/bin/pint --format agent` after all coding

## 7. Verification

- [x] 7.1 Run all tests: `php artisan test --compact` — confirm no regressions (116 tests, all pass)
- [ ] 7.2 Sync specs to main: `openspec sync-specs --change "kan-15-tech-02-structured-analysis-contract"`
- [ ] 7.3 Archive change: `openspec archive "kan-15-tech-02-structured-analysis-contract"`
- [ ] 7.4 Commit and push branch `feature/analyse-ia`
