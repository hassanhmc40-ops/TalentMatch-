## 1. Comparison Analysis Logic

- [x] 1.1 Define scoring weights as class constants (MATCHING_SCORE_WEIGHT, EXPERIENCE_WEIGHT, SKILLS_WEIGHT, EDUCATION_WEIGHT)
- [x] 1.2 Implement matching score dimension (40%): normalize the existing AI matching score
- [x] 1.3 Implement experience dimension (25%): score based on proximity to offer's min_experience_years
- [x] 1.4 Implement skills coverage dimension (25%): ratio of extracted skills count to max count
- [x] 1.5 Implement education dimension (10%): map education_level to numeric score
- [x] 1.6 Compute combined comparison_score as weighted sum of four dimensions
- [x] 1.7 Implement verdict logic: clear recommendation (>=60), nuanced (41-59), toss-up (<=40)
- [x] 1.8 Implement skill gap analysis: set difference of extracted_skills arrays
- [x] 1.9 Add new fields to tool output JSON (comparaison_score, verdict, candidat_recommande, competences_exclusives_*)
- [x] 1.10 Verify backward compatibility: existing fields unchanged

## 2. Test Coverage

- [x] 2.1 Write test: comparison_score is computed correctly with weighted dimensions
- [x] 2.2 Write test: clear verdict when comparison_score >= 60
- [x] 2.3 Write test: nuanced verdict when comparison_score between 41-59
- [x] 2.4 Write test: toss-up verdict when comparison_score <= 40
- [x] 2.5 Write test: skill gap analysis returns correct exclusive skills
- [x] 2.6 Write test: empty exclusive skills when both candidates have identical skills
- [x] 2.7 Write test: backward compatibility — existing fields are still present
- [x] 2.8 Write test: cross-offer comparison still returns error
- [x] 2.9 Write test: unauthorized access still returns error

## 3. Code Quality & Archive

- [x] 3.1 Run `vendor/bin/pint` for code formatting
- [x] 3.2 Run full test suite: `php artisan test --compact` (269 tests, all pass)
- [ ] 3.3 Commit and push to repository
