## Context

Job offer creation (US2) is implemented with a `create` + `store` action on `JobOfferController`. The next step in the HR workflow (US3) requires a paginated listing of the user's offers with candidate counts. Currently there is no `index` method, no listing view, and `viewAny` on `JobOfferPolicy` returns `false`.

## Goals / Non-Goals

**Goals:**
- Provide a paginated listing page at `GET /offres` showing the authenticated user's job offers
- Display title, required skills summary, minimum experience, creation date, and candidate count per offer
- Scope the list to the authenticated user only (no cross-user access)
- Prevent N+1 queries by eager-loading the candidate analyses count
- Add French-language UI consistent with the existing creation form

**Non-Goals:**
- Sorting or filtering (beyond ownership filtering) — deferred to a future iteration
- Offer detail page (US4) — handled in a separate change
- Candidate submission or analysis display — handled in separate changes
- Bulk actions on offers

## Decisions

**1. Ownership scoping via controller query rather than policy**
The `viewAny` policy returns `true`, and the controller query scopes to `user_id`. This keeps the policy simple (it just checks authentication) while the query handles data isolation. The alternative — a policy filtering every index call — would require passing the user context through a separate mechanism.

**2. `withCount('candidateAnalyses')` for candidate counts**
Using Eloquent's `withCount` avoids N+1 by running a single subquery instead of lazy-loading counts per offer. Debugbar must be used during development to verify no additional queries fire.

**3. Pagination at 10 items per page**
Standard Laravel `paginate(10)` with Bootstrap-compatible pagination links. Keeps pages fast and the UI uncluttered.

**4. New Blade view at `resources/views/offres/index.blade.php`**
Consistent structure with the existing `create.blade.php`: extends the same layout and uses French labels.

## Risks / Trade-offs

- **[Performance] Large candidate counts** → `withCount` is efficient even with thousands of records since it's a single `SELECT COUNT(*) ... GROUP BY` subquery.
- **[N+1 regression]** → Mitigated by Debugbar check during development and a dedicated test that asserts no N+1 queries occur.
- **[Pagination link style]** → Laravel's default paginator uses Tailwind-style classes by default; the view uses `resources/views/vendor/pagination/bootstrap-5.blade.php` if Bootstrap is used, or Tailwind if Breeze installed with Tailwind. Verify before implementation.
