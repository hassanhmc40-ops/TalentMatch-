## 1. Recommendation Enum Helpers

- [ ] 1.1 Add `toSelectArray(): array` method to `Recommendation` enum returning `value => label` pairs
- [ ] 1.2 Add `fromLabel(string $label): self` method with `InvalidArgumentException` for invalid labels

## 2. Typed Accessors — CandidateAnalysis

- [ ] 2.1 Add `scoreLevel(): string` method (0-30 → Faible, 31-60 → Moyen, 61-80 → Bon, 81-100 → Excellent)
- [ ] 2.2 Add `isRecommended(): bool` method (true only for Convoquer)
- [ ] 2.3 Add `skillCount(): int` method (count of extracted_skills)
- [ ] 2.4 Add `missingSkillCount(): int` method (count of missing_skills)

## 3. Typed Accessor — JobOffer

- [ ] 3.1 Add `skillCount(): int` method (count of required_skills, handles null/empty)

## 4. Cast Formalization

- [ ] 4.1 Add `education_level` to the `CandidateAnalysis` casts as `'string'`
- [ ] 4.2 Add `@property` PHPDoc annotations to `CandidateAnalysis` for all casted attributes
- [ ] 4.3 Add `@property` PHPDoc annotations to `JobOffer` for casted attributes
- [ ] 4.4 Add `@property` PHPDoc annotations to `Candidate` for all attributes

## 5. Tests

- [ ] 5.1 Test `Recommendation::toSelectArray()` returns correct three options
- [ ] 5.2 Test `Recommendation::fromLabel()` with valid French labels
- [ ] 5.3 Test `Recommendation::fromLabel()` with invalid label throws
- [ ] 5.4 Test `CandidateAnalysis::scoreLevel()` for each range boundary
- [ ] 5.5 Test `CandidateAnalysis::isRecommended()` true/false cases
- [ ] 5.6 Test `CandidateAnalysis::skillCount()` with various skill arrays
- [ ] 5.7 Test `CandidateAnalysis::missingSkillCount()` with various arrays
- [ ] 5.8 Test `JobOffer::skillCount()` with various skill arrays
- [ ] 5.9 Run `vendor/bin/pint --format agent`

## 6. Verification

- [ ] 6.1 Run all tests: `php artisan test --compact`
- [ ] 6.2 Archive change: `openspec archive "kan-19-tech-06-eloquent-casts-enum" --no-validate`
- [ ] 6.3 Commit and push
