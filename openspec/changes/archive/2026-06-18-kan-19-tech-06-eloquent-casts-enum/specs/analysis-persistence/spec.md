## ADDED Requirements

### Requirement: Recommendation enum provides select array

The `Recommendation` enum SHALL provide a `toSelectArray(): array` method returning `value => label` pairs for form dropdowns.

#### Scenario: toSelectArray returns all options
- **WHEN** `Recommendation::toSelectArray()` is called
- **THEN** it SHALL return `['convoquer' => 'À convoquer', 'attente' => 'En attente', 'rejeter' => 'À rejeter']`

### Requirement: Recommendation enum provides reverse label lookup

The `Recommendation` enum SHALL provide a `fromLabel(string $label): self` method that returns the matching case for a French label.

#### Scenario: Valid label returns correct case
- **WHEN** `Recommendation::fromLabel('À convoquer')` is called
- **THEN** it SHALL return `Recommendation::Convoquer`

#### Scenario: Invalid label throws exception
- **WHEN** `Recommendation::fromLabel('Invalide')` is called
- **THEN** it SHALL throw an `InvalidArgumentException`

### Requirement: education_level is cast as string

The `education_level` field on `CandidateAnalysis` SHALL be explicitly cast as `string` in the `casts()` method.

#### Scenario: education_level is cast to string
- **WHEN** a `CandidateAnalysis` is created with `education_level`
- **THEN** Eloquent SHALL return it as a native PHP string

### Requirement: Models have PHPDoc type annotations

Each model SHALL have `@property` PHPDoc annotations documenting the types of all casted attributes.

#### Scenario: CandidateAnalysis has typed property annotations
- **WHEN** the `CandidateAnalysis` class is inspected
- **THEN** it SHALL have `@property` annotations for `int $matching_score`, `array $extracted_skills`, `array $languages`, `array $strengths`, `array $gaps`, `array $missing_skills`, `int $years_experience`, `string $education_level`, `string $justification`, `string $status`, `Recommendation $recommendation`

#### Scenario: JobOffer has typed property annotations
- **WHEN** the `JobOffer` class is inspected
- **THEN** it SHALL have `@property` annotations for `array $required_skills`, `int $min_experience_years`
