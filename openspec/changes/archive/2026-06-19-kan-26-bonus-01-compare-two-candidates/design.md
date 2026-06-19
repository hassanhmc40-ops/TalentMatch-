## Context

The `CompareCandidates` AI tool currently returns raw data (scores, strengths, gaps, recommendation values) without synthesizing a conclusion. The HR agent must manually compare the two candidates. This design adds a structured multi-dimensional comparison analysis that evaluates both candidates and produces a verdict.

## Goals / Non-Goals

**Goals:**
- Enhance `CompareCandidates::handle()` to compute a multi-dimensional comparison score
- Add a `verdict` field explaining which candidate is recommended and why
- Add a `comparison_score` field (0-100) representing the overall comparison assessment
- Add a `recommended_candidate` field indicating which candidate the system recommends
- Add a `skill_gaps` cross-analysis showing skills each candidate has that the other lacks
- Preserve all existing output fields for backward compatibility

**Non-Goals:**
- Database changes or migrations
- UI changes (the enhanced output is consumed by the AI agent and rendered by it)
- Changing the tool's schema/parameters
- Adding a new comparison endpoint

## Decisions

- **Scoring dimensions**: Four weighted dimensions: matching score (40%), experience (25%), skills coverage (25%), education (10%). These weights are defined as constants in the tool class for future adjustment.
- **Verdict logic**: If `comparison_score >= 60` the recommended candidate is clearly identified. If `comparison_score < 60` but > 40, a "nuanced" verdict is returned explaining trade-offs. If `<= 40`, the verdict states the candidates are too close to call definitively.
- **Skill gap analysis**: Compute set difference between `extracted_skills` of each candidate to show unique skills. Included in the output as `competences_exclusives_candidat_1` and `competences_exclusives_candidat_2`.
- **Output structure**: The existing fields (`candidat_1`, `candidat_2`, `difference_score`) remain unchanged. New top-level fields are added: `comparaison_score`, `verdict`, `candidat_recommande`, `competences_exclusives_candidat_1`, `competences_exclusives_candidat_2`.
- **Place in code**: Logic stays in `CompareCandidates::handle()` to avoid creating new classes; if complexity grows, a dedicated `ComparisonService` can be extracted later.

## Risks / Trade-offs

- [Risk] Weighted scoring is subjective → Mitigation: weights are documented constants; HR agent can adjust them. Default weights are based on typical HR priorities.
- [Risk] Edge case where both candidates have identical scores → Mitigation: comparison_score handles this by examining secondary dimensions (experience, skills count) to break ties; if truly indistinguishable, verdict states no clear winner.
- [Risk] Large skill arrays slow the tool → Mitigation: array operations are O(n) and skills lists are typically small (< 20 items); no performance concern.
