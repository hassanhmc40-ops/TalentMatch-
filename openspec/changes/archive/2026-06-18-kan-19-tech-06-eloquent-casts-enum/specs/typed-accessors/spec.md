## ADDED Requirements

### Requirement: CandidateAnalysis has scoreLevel accessor

The `CandidateAnalysis` model SHALL provide a `scoreLevel(): string` method that returns a French label for the matching score range.

#### Scenario: Score 0-30 returns Faible
- **WHEN** `$analysis->matching_score` is between 0 and 30
- **THEN** `$analysis->scoreLevel()` SHALL return `"Faible"`

#### Scenario: Score 31-60 returns Moyen
- **WHEN** `$analysis->matching_score` is between 31 and 60
- **THEN** `$analysis->scoreLevel()` SHALL return `"Moyen"`

#### Scenario: Score 61-80 returns Bon
- **WHEN** `$analysis->matching_score` is between 61 and 80
- **THEN** `$analysis->scoreLevel()` SHALL return `"Bon"`

#### Scenario: Score 81-100 returns Excellent
- **WHEN** `$analysis->matching_score` is between 81 and 100
- **THEN** `$analysis->scoreLevel()` SHALL return `"Excellent"`

### Requirement: CandidateAnalysis has isRecommended accessor

The `CandidateAnalysis` model SHALL provide a `isRecommended(): bool` method that returns `true` when the recommendation is `convoquer`.

#### Scenario: Recommended returns true
- **WHEN** `$analysis->recommendation` is `Recommendation::Convoquer`
- **THEN** `$analysis->isRecommended()` SHALL return `true`

#### Scenario: Non-recommended returns false
- **WHEN** `$analysis->recommendation` is `Recommendation::Attente` or `Recommendation::Rejeter`
- **THEN** `$analysis->isRecommended()` SHALL return `false`

### Requirement: CandidateAnalysis has skillCount accessor

The `CandidateAnalysis` model SHALL provide a `skillCount(): int` method that returns the count of extracted skills.

#### Scenario: skillCount returns correct count
- **WHEN** `$analysis->extracted_skills` is `['PHP', 'Laravel', 'MySQL']`
- **THEN** `$analysis->skillCount()` SHALL return `3`

#### Scenario: No skills returns 0
- **WHEN** `$analysis->extracted_skills` is `[]`
- **THEN** `$analysis->skillCount()` SHALL return `0`

### Requirement: CandidateAnalysis has missingSkillCount accessor

The `CandidateAnalysis` model SHALL provide a `missingSkillCount(): int` method that returns the count of missing skills.

#### Scenario: missingSkillCount returns correct count
- **WHEN** `$analysis->missing_skills` is `['Docker', 'Kubernetes']`
- **THEN** `$analysis->missingSkillCount()` SHALL return `2`

### Requirement: JobOffer has skillCount accessor

The `JobOffer` model SHALL provide a `skillCount(): int` method that returns the count of required skills.

#### Scenario: skillCount returns correct count
- **WHEN** `$offer->required_skills` is `['PHP', 'MySQL', 'Laravel']`
- **THEN** `$offer->skillCount()` SHALL return `3`

#### Scenario: No required skills returns 0
- **WHEN** `$offer->required_skills` is `[]` or `null`
- **THEN** `$offer->skillCount()` SHALL return `0`
