## Context

The app already has:
- `Recommendation` enum with three cases and a `label()` method
- `CandidateAnalysis` with full array/int/enum/string casts
- `JobOffer` with `required_skills` array cast and `min_experience_years` integer cast
- `User` with standard `datetime` and `hashed` casts
- `Candidate` without any casts (only string/text columns)
- Existing `analysis-persistence` spec covering persistence mapping and basic casts

Missing: typed accessor methods on models, enum helper methods for dropdowns/reverse lookup, formal cast documentation in PHPDoc blocks for each model.

## Goals / Non-Goals

**Goals:**
- Add typed accessor methods to `CandidateAnalysis`: `scoreLevel(): string`, `isRecommended(): bool`, `skillCount(): int`, `missingSkillCount(): int`
- Add typed accessor to `JobOffer`: `skillCount(): int`
- Add helper methods to `Recommendation` enum: `toSelectArray(): array`, `fromLabel(string): self`
- Add PHPDoc property type hints for casted attributes on all models
- Document the full casts contract in the spec

**Non-Goals:**
- No new migrations or database changes
- No UI changes
- No changes to the AI analysis flow
- No new controllers, jobs, or services

## Decisions

1. **Accessor methods over `Attribute` pattern** — Use `public function` methods (not Laravel's `Attribute` get/set pattern) since these are computed/convenience values, not attribute transformations. Simpler, more explicit.

2. **`scoreLevel()` returns French string** — Maps `matching_score` to: 0-30 → "Faible", 31-60 → "Moyen", 61-80 → "Bon", 81-100 → "Excellent". Follows the French UI convention.

3. **Enum `toSelectArray()` returns `value => label`** — Standard Laravel pattern for `<select>` dropdowns. The `fromLabel()` method provides reverse lookup for form submission.

4. **PHPDoc `@property` annotations** — Add `@property int $matching_score`, `@property array $extracted_skills`, etc. on each model so IDE autocompletion and static analysis know the types.

## Risks / Trade-offs

- **[Risk] Accessor names conflict with existing relationships** — Mitigation: prefix any ambiguous names (e.g., `isRecommended()` not `recommended()`)
- **[Trade-off] `scoreLevel()` is a simple if/else chain** — This is intentional per the config.yaml "do not do" rule: the AI determines the score, the accessor just formats it for display
