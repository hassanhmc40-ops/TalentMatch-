## 1. System Prompt Redesign

- [x] 1.1 Rewrite `CvAnalysisAgent::instructions()` with structured sections: role, task, field rules, score logic, anti-hallucination guard, recommendation criteria
- [x] 1.2 Add field-by-field output rules for each of the 10 schema fields in `instructions()`
- [x] 1.3 Add score calculation criteria description (skills match, experience, languages, education) — no hardcoded formula
- [x] 1.4 Add anti-hallucination clause: "ne pas inventer" for skills, experience, languages, and education
- [x] 1.5 Add explicit missing-data handling: empty arrays for missing multi-value fields, "Non spécifié" for missing education
- [x] 1.6 Add recommendation criteria: "convoquer" if score >= 70 and skills match, "attente" if 40-69, "rejeter" if < 40

## 2. Few-Shot Examples

- [x] 2.1 Create a representative sample CV text with clear skills, experience, education, languages
- [x] 2.2 Create a corresponding sample job offer with title, description, skills, and min experience
- [x] 2.3 Write the expected JSON output for the sample data matching the schema
- [x] 2.4 Append the few-shot example(s) to the user prompt in `AnalyseCvJob::handle()` or `CvAnalysisAgent::prompt()`
- [x] 2.5 Verify CV and offer data is still included above the few-shot examples

## 3. Test Coverage

- [x] 3.1 Write test: `instructions()` contains role definition section
- [x] 3.2 Write test: `instructions()` contains anti-hallucination clauses
- [x] 3.3 Write test: `instructions()` contains missing-data handling rules
- [x] 3.4 Write test: `instructions()` mentions skills, experience, languages, education in score logic
- [x] 3.5 Write test: prompt built by `AnalyseCvJob` contains at least one few-shot example
- [x] 3.6 Write test: few-shot example includes expected JSON output matching schema
- [x] 3.7 Write test: repeated calls with identical input produce identical structure
- [x] 3.8 Write test: empty CV results in empty arrays / "Non spécifié" per prompt instructions

## 4. Edge Cases

- [x] 4.1 Ensure CV text with no extractable skills produces empty `competences_extraites` array
- [x] 4.2 Ensure job offer with empty `required_skills` does not cause prompt errors
- [x] 4.3 Ensure very long CV text (>5000 chars) is truncated or handled gracefully before prompt building

## 5. Final Verification

- [x] 5.1 Run full test suite: `php artisan test --compact`
- [x] 5.2 Run `vendor/bin/pint --format agent` for code style
- [x] 5.3 Verify no regression in existing CV analysis integration tests
