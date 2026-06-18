## Context

TalentMatch requires Form Requests for all user-facing data input (config.yaml). The `StoreJobOfferRequest` was implemented as a basic Form Request in the previous change (KAN-4 US2). It now needs formal validation spec alignment — authorization policy integration, input sanitization via `prepareForValidation()`, and French attribute names via `attributes()`. Additionally, US5 (candidate submission) requires a new `SubmitCandidateRequest` with its own validation rules, including duplicate detection and ownership verification.

## Goals / Non-Goals

**Goals:**
- Add `authorize()` to `StoreJobOfferRequest` using `JobOfferPolicy::create`
- Add `prepareForValidation()` to trim/sanitize inputs in both requests
- Add `attributes()` with French field names for cleaner error messages in both requests
- Create `SubmitCandidateRequest` with validation for: candidate name, CV text, job offer ID (must exist, must belong to user)
- Create `Candidate` model and migration (`candidates` table) as prerequisite for submission
- Add duplicate candidate detection (same name + same offer_id)
- Add French validation messages for all new rules
- Cover all validation edge cases in specs and tests

**Non-Goals:**
- Job offer CRUD (US3, US4) — separate changes
- AI analysis (US6) — separate change
- Candidate analysis detail page (US7) — separate change
- Controller implementation for submission — handled in a separate US5 change
- View layer for submission forms — handled in separate US5 change

## Decisions

| Decision | Choice | Rationale | Alternatives Considered |
|---|---|---|---|
| Authorization approach | Use existing `JobOfferPolicy::create` in authorize() | Keeps authorization logic in one place; policy already exists and handles create permission | Inline `auth()->check()` — redundant, policy already checks auth |
| Input sanitization | `prepareForValidation()` to trim strings and filter empty skills | Prevents whitespace-only input from passing `required` validation; removes empty skill array entries | Mutator in model — less appropriate for request-level concerns |
| Duplicate detection | Check in `withValidator()` after rule validation | Allows returning user-friendly French error; avoids DB query when other validations fail | Custom rule class — over-engineered for a single check |
| French attributes | `attributes()` method on Form Request | Overrides default `:attribute` placeholder in validation messages without customizing each message | Inline `:attribute` in each message — more verbose, harder to maintain |
| Candidate table | Separate `candidates` table with `name`, `cv_text`, linked to analyses | Follows domain model: candidate is an entity that exists independently of a single analysis; avoids duplicating name/CV across analyses | Store candidate inline on analysis — violates normalization, makes deduplication impossible |
| CV text length limit | 50,000 characters (MySQL TEXT max is ~65KB) | Allows legitimate long CVs while preventing abuse; matches MySQL TEXT practical limit | No limit — risk of oversized payloads; VARCHAR(65535) — arbitrary truncation |

## Form Request Architecture

### StoreJobOfferRequest (Modified)

```
authorize()          → JobOfferPolicy::create
prepareForValidation → trim title/description, filter empty skills
rules()              → unchanged (title, description, required_skills, min_experience_years)
messages()           → unchanged (French messages)
attributes()         → French field names: "titre", "description", "compétences", "années d'expérience"
```

### SubmitCandidateRequest (New)

```
authorize()          → true (auth middleware handles authentication; ownership checked via offer_id validation)
prepareForValidation → trim name/cv_text
rules()              → nom (string, max:255), cv_text (string, min:1, max:50000), offre_id (exists:job_offers,id)
withValidator()      → duplicate check: Candidate with same name + offre_id already exists
messages()           → French error messages
attributes()         → French field names: "nom du candidat", "texte du CV", "offre d'emploi"
```

## Database Schema (Candidate)

```sql
CREATE TABLE candidates (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(255) NOT NULL,
    cv_text     TEXT NOT NULL,
    created_at  TIMESTAMP NULL,
    updated_at  TIMESTAMP NULL
);
```

No foreign key to users — candidates are scoped through job offers per domain model.

## Duplicate Detection Strategy

The `withValidator()` callback in `SubmitCandidateRequest` will:

1. Check if `Candidate` exists where `name` matches (case-insensitive) AND is linked to the same `job_offer_id` via an existing `CandidateAnalysis`
2. If found, add a custom validation error: "Ce candidat a déjà été soumis pour cette offre."
3. This check runs only after all rule validations pass (to avoid unnecessary DB queries)

Since `CandidateAnalysis` doesn't exist yet (US6), the duplicate check will verify against the `candidates` table directly — a future change can add the analysis relationship check.

## Risks / Trade-offs

| Risk | Impact | Mitigation |
|---|---|---|
| **Duplicate detection too early** — table/model may change | Need to update check logic when CandidateAnalysis is added | Keep the check simple in v1; refactor when US6 adds the analysis model |
| **CV text 50K limit too restrictive** | Legitimate long CVs rejected | Monitor usage; increase to MEDIUMTEXT if needed |
| **No direct user-candidate relation** | Cannot quickly list all candidates across all offers for a user | Query through job_offers join; acceptable for v1 per domain model |
| **prepareForValidation() masking errors** | Silent data loss if trimming removes meaningful content | Log original values if trimmed content differs significantly |
