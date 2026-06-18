## Why

Form Requests are mandatory for all data input in TalentMatch (per config.yaml tech stack). The existing `StoreJobOfferRequest` covers US2 but needs a formal validation spec. US5 (candidate submission) has no Form Request yet — without one, CV text and candidate data could be saved unvalidated, creating corrupted records and bypassing the structured AI contract. This change formalizes both request validations as a dedicated technical improvement (KAN-20 TECH-07).

## What Changes

- **`StoreJobOfferRequest`** — formal review and specification of existing validation rules; add authorization check (user must be authenticated), add `prepareForValidation()` to trim/sanitize input, add French `attributes()` for cleaner error messages
- **`SubmitCandidateRequest`** — new Form Request for US5 with validation rules for candidate name, CV text, and job offer ID ownership check
- **`Candidate` model** and migration (required by US5 before submission is possible)
- **French validation messages and attribute names** for all new and existing rules
- **Edge case coverage**: empty CV text, empty name, duplicate submission for same offer, very long CV text

## Capabilities

### New Capabilities
- `candidate-submission`: Validation for submitting a candidate name and CV text against a job offer, including ownership verification and duplicate detection.

### Modified Capabilities
- `job-offer-creation`: Add authorization check (authenticated user), `prepareForValidation()` sanitization, and `attributes()` French naming to the Form Request. These are spec-level behavior additions.

## Impact

- **Modified**: `app/Http/Requests/StoreJobOfferRequest.php` — add `authorize()`, `prepareForValidation()`, `attributes()`
- **New**: `app/Http/Requests/SubmitCandidateRequest.php` — validation rules for candidate submission
- **New**: `App\Models\Candidate` model with migration for `candidates` table
- **New**: `database/migrations/*_create_candidates_table.php`
- **Tests**: New tests for `SubmitCandidateRequest` validation; update existing `JobOfferCreationTest` for authorization changes
- **Views**: No view changes — requests are consumed by controllers handled in separate changes (US5)
