## ADDED Requirements

### Requirement: Analysis is dispatched via queue after submission validation

The system SHALL dispatch a queue job to perform AI analysis after the form validation passes and the Candidate record is created.

#### Scenario: Queue job is dispatched after valid submission
- **WHEN** an HR user submits valid candidate data for a job offer
- **THEN** the system SHALL create a Candidate record
- **AND** SHALL dispatch a queue job (e.g., `AnalyseCvJob`) with the candidate ID and job offer ID
- **AND** SHALL return a success response to the user without waiting for the AI analysis to complete

#### Scenario: Queue job receives validation-gated analysis
- **WHEN** the queue job receives the AI response
- **THEN** the job SHALL pass the response through the structured analysis validation layer
- **AND** if validation passes, SHALL persist the mapped data via `CandidateAnalysis::create()`
- **AND** if validation fails, SHALL NOT persist, SHALL log the error, and SHALL fail the job

### Requirement: Queue job failure is handled gracefully

The system SHALL handle queue job failures without data corruption and surface the error to the user.

#### Scenario: Job fails and candidate shows pending analysis
- **WHEN** the `AnalyseCvJob` fails (validation error, AI timeout, or exception)
- **THEN** the system SHALL NOT create a corrupted `CandidateAnalysis` record
- **AND** the candidate SHALL remain visible in the offer detail with a "Analyse en cours" or "Échec de l'analyse" status

#### Scenario: Failed job is released for retry
- **WHEN** the `AnalyseCvJob` fails due to a transient error (AI timeout, network error)
- **THEN** the job SHALL be released back to the queue with a delay
- **AND** the system SHALL use the default Laravel job retry mechanism

### Requirement: Analysis status is tracked

The system SHALL track the analysis state so the UI can display pending, successful, or failed status.

#### Scenario: Analysis has status field
- **WHEN** a `CandidateAnalysis` record exists
- **THEN** it SHALL have a `status` column with values: `pending`, `completed`, `failed`
- **AND** a new analysis starts as `pending`, becomes `completed` after successful persistence, or `failed` if validation/job fails
