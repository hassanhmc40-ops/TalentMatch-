## Context

The offer detail page currently displays candidates in arbitrary collection order (typically insertion order). The existing compare feature works with candidate checkboxes but does not surface the best matches. All required data (matching_score, years_experience, education_level, extracted_skills) already exists on CandidateAnalysis — no new schema changes needed.

## Goals / Non-Goals

**Goals:**
- Candidates on the offer detail page SHALL be ordered by matching_score descending
- Tie-breaking: when scores are equal, order by years_experience descending, then skills count descending, then education level weight descending
- Each candidate row SHALL display a rank number (1, 2, 3…) indicating position
- A score progress bar SHALL provide a quick visual comparison of relative scores
- The ranking SHALL be available as a reusable Eloquent scope and Blade component

**Non-Goals:**
- No new database columns or migrations
- No multi-offer global ranking (ranking is always scoped to a single offer)
- No AI-powered reranking — ranking uses stored analysis data only
- No API endpoint for ranking (scope is within the existing offer detail page)
- No changes to the comparison flow (selected candidates remain selected regardless of order)

## Decisions

1. **Eloquent scope over controller sorting** — Adding `scopeRanked()` to CandidateAnalysis keeps ordering logic in the model where it can be reused by any query (dashboard, API, tools). Controller-only sorting would need to be duplicated.

2. **Tie-breaking order** — `matching_score DESC` is primary. For ties, `years_experience DESC` rewards more experienced candidates. Next, skills count (computed from `extracted_skills` array length) breaks ties by breadth. Finally, education level uses the same mapping as `CompareCandidates` (`computeEducationDimension`) to ensure consistency. This avoids non-deterministic ordering when two candidates have identical scores.

3. **Rank number computed in-memory** — Using `@index + 1` in the Blade template (or an accessor) rather than a database column avoids stale data and migration overhead. The rank is always correct for the current query result.

4. **Score progress bar** — Reuse the existing `<x-progress>` component with `max="100"` to show relative score. This gives an immediate visual sense of how candidates compare.

5. **Integrated into existing page** — No separate ranking route or tab. Candidates remain under the "Candidats analysés" section, now visually ranked with a leaderboard-style presentation. This minimizes UX disruption while delivering the core value.

## Risks / Trade-offs

- **[Risk] Large number of candidates** → The existing table is already rendered in full. Ranking doesn't add overhead since the same collection is loaded. Pagination could be added later if needed.
- **[Risk] Ties still possible after 4-level tie-breaking** → Extremely unlikely but theoretically possible. The order among truly equal rows is non-deterministic (MySQL). Mitigation: the rank number makes equality visible to the user.
- **[Trade-off] No database rank column** → Rank is not persisted, which means leaderboard position cannot be used in AI tool queries or notifications. If needed later, a computed column or view can be added.
