## Context

TalentMatch is a Laravel application for Moroccan HR teams to screen candidates via AI. Currently the project has only authentication (Breeze scaffold). No job offer, candidate, or analysis models exist. The database contains only the default `users`, `sessions`, `cache`, `jobs`, and `telescope` tables.

This design covers US2: the first domain feature — creating a job offer. Every subsequent feature (candidate submission, AI analysis, conversational agent) depends on job offers existing as the aggregation root.

## Goals / Non-Goals

**Goals:**
- Provide an authenticated HR user with a form to create a job offer (French UI)
- Store job offers with title, description, required skills (array), and minimum experience level
- Enforce that every job offer belongs to the authenticated user (user_id foreign key)
- Validate input via a Form Request before persisting
- Authorize access via a Laravel Policy
- Redirect to the offer list (or offer detail) upon successful creation with a success flash message
- Cover the creation flow with Pest feature tests
- Use Eloquent casts for the `required_skills` JSON column

**Non-Goals:**
- Job offer listing (US3) — separate change
- Job offer detail page (US4) — separate change
- Job offer editing or deletion — separate change
- Candidate submission or AI analysis — separate changes
- Soft-deletes or archival — not required by current scope
- API versioning or JSON API — this is a web-only feature using Blade

## Decisions

| Decision | Choice | Rationale | Alternatives Considered |
|---|---|---|---|
| Model name | `JobOffer` with table `job_offers` | Clear English name per project convention; config.yaml explicitly uses "Offre / JobOffer" for the model | `Offre` (French) — rejected because the project requires technical code in English |
| Route prefix | `offres.*` (French) | The URL shown to HR users should be in French (`/offres/creer`). The naming follows the UI language requirement. | `job-offers.*` — rejected because UI language is French |
| Required skills storage | JSON column with Eloquent `array` cast | Simplest approach for storing a variable-length list of skills. MySQL JSON type is well-supported in Laravel. | Separate `skills` table — over-engineered for this scope; a normalized table makes sense only if skills become a shared reference entity |
| Minimum experience storage | Integer column (`min_experience_years`) | Direct numeric field — no casting needed. The UI can show "Années d'expérience requises". | String field — rejected because it would require parsing for future comparison with candidate experience |
| Form validation | Dedicated `StoreJobOfferRequest` Form Request | Separates validation logic from controller; reusable if update endpoint is added later. Matches project requirement: "Form Requests are mandatory for job offer creation/update". | Inline validation in controller — violates project convention |
| Authorization | `JobOfferPolicy` with `create` method | Policy pattern is the Laravel standard for authorization. Even though creation only requires the user to be authenticated, using a policy makes it consistent with future operations (view, update, delete) that will check ownership. | Gate-based — less discoverable; policies are the recommended approach for model-based authorization |
| View location | `resources/views/offres/` | Matches existing `resources/views/profile/` convention. | `resources/views/job-offers/` — inconsistent with French route prefix |
| Success feedback | Flash message in session using `with('success', ...)` | Standard Laravel pattern; the layout already supports `x-auth-session-status` for messages | Toast notifications — adds unnecessary complexity for v1 |
| Database index | Composite index on `(user_id, created_at)` | Optimizes the future listing query (US3) where a user views their offers sorted by date. Single-column `user_id` index is added implicitly by the FK. | No index — would cause full table scans on user-scoped queries as data grows |
| Testing approach | Pest feature test using `RefreshDatabase` | Follows project test stack. Factory will generate sample job offers for create/store tests. | PHPUnit — project explicitly allows both; Pest is more concise |

## Skills Storage Strategy

Job offer required skills are stored as a JSON array in the `required_skills` column:

```json
["PHP", "Laravel", "MySQL", "Git"]
```

This approach is chosen because:
- Skills are free-text tags entered by the HR agent at creation time
- No shared skills taxonomy exists (and building one is out of scope)
- The array cast makes the data accessible as a native PHP array in Eloquent
- Future comparison logic will check candidate extracted skills against this array using simple `array_intersect()` or similar

If a shared skills catalog becomes necessary later, a pivot table migration can be introduced without breaking existing data.

## Authorization Rules

- **Create (store)**: Any authenticated user can create a job offer. The offer is automatically assigned to `$this->user()->id`.
- **Ownership enforcement**: The `user_id` is set from `auth()->id()` in the controller, never from user-submitted input. This prevents privilege escalation.
- **Future operations** (view, update, delete): Policy will check `$user->id === $jobOffer->user_id`.

The `JobOfferPolicy` is registered in `AuthServiceProvider`.

## Database Schema

```sql
CREATE TABLE job_offers (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id         BIGINT UNSIGNED NOT NULL,
    title           VARCHAR(255) NOT NULL,
    description     TEXT NOT NULL,
    required_skills JSON NOT NULL,
    min_experience_years INTEGER UNSIGNED NOT NULL DEFAULT 0,
    created_at      TIMESTAMP NULL,
    updated_at      TIMESTAMP NULL,

    CONSTRAINT fk_job_offers_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_job_offers_user_created (user_id, created_at)
);
```

## Route Design

| Method | URI | Name | Controller | Middleware |
|---|---|---|---|---|
| GET | `/offres/creer` | `offres.create` | `JobOfferController@create` | `auth`, `verified` |
| POST | `/offres` | `offres.store` | `JobOfferController@store` | `auth`, `verified` |

## Validation Rules

Defined in `StoreJobOfferRequest`:

| Field | Rule | Message (FR) |
|---|---|---|
| `title` | required, string, max:255 | Le titre est obligatoire. |
| `description` | required, string, min:10 | La description doit contenir au moins 10 caractères. |
| `required_skills` | required, array, min:1 | Au moins une compétence est requise. |
| `required_skills.*` | required, string, max:100, distinct | Chaque compétence doit être une chaîne unique. |
| `min_experience_years` | required, integer, min:0, max:50 | L'expérience doit être comprise entre 0 et 50 ans. |

## View Structure

```
resources/views/offres/
  create.blade.php       — Form with French labels and error display
```

The form extends the Breeze `layouts/app` layout. The navigation sidebar gets a "Créer une offre" link pointing to `route('offres.create')`.

## Risks / Trade-offs

| Risk | Impact | Mitigation |
|---|---|---|
| **Skills as JSON** — no referential integrity for skills | Duplicate/homonym skills (e.g., "JS" vs "JavaScript") across offers | Acceptable for v1; a future normalization migration can deduplicate |
| **No edit/delete in scope** — user cannot correct a mistake | User frustration if they mistype an offer | Implement edit/delete in a follow-up change (US3/US4 scope) |
| **Queue not involved** — creation is synchronous | No risk here — offer creation is a simple DB insert | Analysis will use queues (US6 scope) |
| **Policy dependency** — future-policy for create could be over-engineered | Minimal extra effort; the create policy simply returns `true` for auth users | Consistent architecture pays off when view/update/delete policies are added later |
