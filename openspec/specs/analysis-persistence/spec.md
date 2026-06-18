# analysis-persistence Specification

## Purpose
TBD - created by archiving change kan-15-tech-02-structured-analysis-contract. Update Purpose after archive.
## Requirements
### Requirement: French AI response keys are mapped to English DB columns

The system SHALL map each French key from the AI JSON response to the corresponding English database column before persistence.

#### Scenario: Full key mapping is applied
- **WHEN** the AI returns `{"competences_extraites":[...], "annees_experience":5, "niveau_etudes":"Bac+5", "langues":[...], "matching_score":78, "points_forts":[...], "lacunes":[...], "competences_manquantes":[...], "recommandation":"convoquer", "justification":"..."}`
- **THEN** the data SHALL be persisted with English column names: `extracted_skills`, `years_experience`, `education_level`, `languages`, `matching_score`, `strengths`, `gaps`, `missing_skills`, `recommendation`, `justification`

### Requirement: Array fields are persisted as JSON

Array fields (`extracted_skills`, `languages`, `strengths`, `gaps`, `missing_skills`) SHALL be persisted in JSON columns using Eloquent's `array` cast.

#### Scenario: Array fields are saved and retrieved as arrays
- **WHEN** a `CandidateAnalysis` is created with array values
- **THEN** the database stores them as JSON
- **AND** when retrieved, Eloquent returns them as native PHP arrays

### Requirement: recommandation is persisted using enum cast

The `recommendation` field SHALL use the `Recommendation` enum cast.

#### Scenario: Enum cast transforms string to enum
- **WHEN** the validated string `"convoquer"` is assigned to the `recommendation` attribute
- **THEN** Eloquent SHALL cast it to `App\Enums\Recommendation::Convoquer`
- **AND** when retrieved from the database, it SHALL be a `Recommendation` enum instance

#### Scenario: French label is accessible via enum
- **WHEN** `$analysis->recommendation->label()` is called
- **THEN** it SHALL return the French label: `"À convoquer"`, `"En attente"`, or `"À rejeter"`

### Requirement: matching_score is persisted as integer

The `matching_score` field SHALL use Eloquent's `integer` cast.

#### Scenario: Integer cast is applied
- **WHEN** a validated integer score is persisted
- **THEN** the database column stores an integer
- **AND** Eloquent returns it as a native PHP integer

### Requirement: Persistence is wrapped in validation gate

The system SHALL NOT persist an analysis that has not passed validation. Persistence SHALL only execute after the validation layer confirms all rules pass.

#### Scenario: Invalid analysis is not persisted
- **WHEN** validation fails
- **THEN** no `CandidateAnalysis` record SHALL be created
- **AND** the queue job SHALL fail with a clear error

#### Scenario: Valid analysis is persisted
- **WHEN** validation passes
- **THEN** a `CandidateAnalysis` record SHALL be created with all mapped fields
- **AND** the record SHALL be associated with the correct `job_offer_id` and `candidate_id`

### Requirement: Duplicate analysis detection

The system SHALL prevent creating a second analysis for the same candidate + offer combination.

#### Scenario: Duplicate analysis is rejected
- **WHEN** an analysis already exists for candidate ID X and job offer ID Y
- **THEN** persisting a new analysis SHALL fail with a unique constraint violation or application-level guard

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

