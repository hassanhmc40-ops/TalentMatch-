## Purpose

When a candidate CV is submitted against a job offer, the system SHALL call the AI with a strict JSON schema, validate the structured response, and persist the extracted data. This capability covers the prompt strategy, agent setup (`CvAnalysisAgent`), structured output contract, and integration with the queue job.

## Requirements

### Requirement: Agent is configured with French system instructions

The `CvAnalysisAgent` SHALL define system-level instructions in French that describe the CV analysis task.

#### Scenario: Instructions are in French
- **WHEN** inspecting `CvAnalysisAgent::instructions()`
- **THEN** the instructions SHALL be in French
- **AND** SHALL describe extracting structured information from CV text and matching against job offer requirements

### Requirement: Structured output schema defines all required fields

The `CvAnalysisAgent` SHALL define a JSON schema via `schema()` that includes all fields from the structured output contract.

#### Scenario: Schema includes all required fields with correct types
- **WHEN** inspecting `CvAnalysisAgent::schema()`
- **THEN** it SHALL define the following fields:
  - `competences_extraites` — array of strings (required)
  - `annees_experience` — integer ≥ 0 (required)
  - `niveau_etudes` — string (required)
  - `langues` — array of strings (required)
  - `matching_score` — integer between 0 and 100 (required)
  - `points_forts` — array of strings (required)
  - `lacunes` — array of strings (required)
  - `competences_manquantes` — array of strings (required)
  - `recommandation` — string, one of `convoquer`, `attente`, `rejeter` (required)
  - `justification` — string (required)

### Requirement: Prompt is built from candidate CV and job offer data

The `AnalyseCvJob` SHALL build a French-language prompt containing the candidate's CV text and the job offer's title, description, required skills, and minimum experience. The prompt SHALL also include few-shot examples demonstrating the expected output format.

#### Scenario: Prompt contains CV text and offer details
- **WHEN** `AnalyseCvJob::handle()` runs
- **THEN** the prompt passed to `CvAnalysisAgent::prompt()` SHALL contain:
  - The candidate's `cv_text`
  - The job offer title
  - The job offer description
  - The job offer required skills
  - The job offer minimum experience years
  - At least one few-shot example with expected JSON output

#### Scenario: Few-shot examples show complete input-to-output transformation
- **WHEN** inspecting the built prompt
- **THEN** each few-shot example SHALL include a sample CV, a sample job offer, and the complete expected JSON output matching the schema

### Requirement: AI response is validated against structured contract

After the AI returns a response, the system SHALL validate it using `ValidateStructuredAnalysis` before any persistence.

#### Scenario: Valid AI response passes validation
- **WHEN** the AI returns a complete JSON response matching all schema constraints
- **THEN** `ValidateStructuredAnalysis::validate()` SHALL return the mapped data with English column keys
- **AND** persistence SHALL proceed

#### Scenario: Invalid AI response is rejected
- **WHEN** the AI returns a response with missing fields, wrong types, or out-of-range values
- **THEN** `ValidateStructuredAnalysis::validate()` SHALL throw a `ValidationFailedException`
- **AND** the analysis status SHALL be set to `failed`
- **AND** the raw AI response SHALL NOT be exposed in the UI

### Requirement: Validated data is persisted with mapped column names

After validation passes, the system SHALL persist the analysis using `PersistValidatedAnalysis` with English column names.

#### Scenario: Analysis is saved with English keys
- **WHEN** validated data is persisted
- **THEN** the `CandidateAnalysis` record SHALL use English column names (e.g. `extracted_skills`, `matching_score`)
- **AND** the `status` SHALL be set to `completed`
- **AND** the record SHALL be associated with the correct `job_offer_id` and `candidate_id`

### Requirement: French field names map to English DB columns

The system SHALL map each French key from the AI JSON to an English database column via `ValidateStructuredAnalysis::FRENCH_TO_ENGLISH`.

#### Scenario: Complete key mapping is applied
- **WHEN** a valid AI response is processed
- **THEN** the following mapping SHALL be applied:
  - `competences_extraites` → `extracted_skills`
  - `annees_experience` → `years_experience`
  - `niveau_etudes` → `education_level`
  - `langues` → `languages`
  - `matching_score` → `matching_score`
  - `points_forts` → `strengths`
  - `lacunes` → `gaps`
  - `competences_manquantes` → `missing_skills`
  - `recommandation` → `recommendation`
  - `justification` → `justification`
