## ADDED Requirements

### Requirement: Comparison uses multi-dimensional weighted scoring

The `CompareCandidates` tool SHALL compute a multi-dimensional comparison score (0-100) that evaluates both candidates across four weighted dimensions.

#### Scenario: Comparison score is computed from four dimensions
- **WHEN** the tool receives two valid analysis IDs
- **THEN** the comparison score SHALL be computed using matching score (40%), years of experience (25%), skills coverage (25%), and education level (10%)
- **AND** the result SHALL include a `comparaison_score` field (integer 0-100)

#### Scenario: Skills coverage measures extracted skills count
- **WHEN** both candidates have completed analyses
- **THEN** skills coverage SHALL be computed as a ratio of each candidate's extracted skills count to the maximum skills count among both candidates, normalized to 0-100

#### Scenario: Experience score is normalized to a target range
- **WHEN** the job offer specifies a minimum experience requirement
- **THEN** experience SHALL be scored based on how close the candidate's years of experience are to the offer's minimum, with a maximum score at the minimum and decreasing for under/over

#### Scenario: Education dimension maps levels to numeric values
- **WHEN** computing the education dimension
- **THEN** education levels SHALL be mapped to numeric scores: Doctorat (100), Bac+5 (80), Bac+3 (60), Bac+2 (40), Bac (20), others (0)

### Requirement: Comparison produces a verdict and recommended candidate

The tool output SHALL include a `verdict` string and a `candidat_recommande` field indicating which candidate is recommended.

#### Scenario: Verdict recommends the higher-scoring candidate
- **WHEN** `comparaison_score` for candidate A is >= 60
- **THEN** `candidat_recommande` SHALL equal "candidat_1"
- **AND** `verdict` SHALL include "recommandé" and a brief French justification

#### Scenario: Nuanced verdict when scores are close
- **WHEN** `comparaison_score` is between 41 and 59
- **THEN** `verdict` SHALL describe a nuanced recommendation explaining trade-offs between the two candidates
- **AND** `candidat_recommande` SHALL indicate the slightly stronger candidate with a caveat

#### Scenario: Toss-up verdict when candidates are very close
- **WHEN** `comparaison_score` is <= 40
- **THEN** `verdict` SHALL state that the candidates are very close
- **AND** `candidat_recommande` SHALL be null or indicate no clear preference

### Requirement: Skill gap analysis identifies unique skills

The tool SHALL compute exclusive skills for each candidate — skills one candidate has that the other does not.

#### Scenario: Exclusive skills are listed per candidate
- **WHEN** both candidates have completed analyses with extracted skills
- **THEN** `competences_exclusives_candidat_1` SHALL list skills in candidate 1's `extracted_skills` but not in candidate 2's
- **AND** `competences_exclusives_candidat_2` SHALL list skills in candidate 2's `extracted_skills` but not in candidate 1's

#### Scenario: Empty exclusive skills
- **WHEN** both candidates have identical skill sets
- **THEN** both exclusive skills arrays SHALL be empty

### Requirement: Backward compatibility is maintained

The enhanced tool output SHALL preserve all existing fields from the base `compareCandidates` response.

#### Scenario: Existing fields are unchanged
- **WHEN** the tool returns its enhanced response
- **THEN** `candidat_1`, `candidat_2`, and `difference_score` SHALL still be present with the same structure as before
- **AND** the response SHALL still be valid JSON
