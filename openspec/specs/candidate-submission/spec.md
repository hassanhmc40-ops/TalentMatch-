## Purpose

HR agents need to submit candidate information (name and CV text) for job offers. This capability covers validating the submission data, detecting duplicate candidates, and providing French-language error feedback.
## Requirements
### Requirement: Candidate name is validated

The system SHALL require a non-empty candidate name with a maximum length of 255 characters.

#### Scenario: Valid name passes
- **WHEN** an HR user submits a candidate name "Jean Dupont"
- **THEN** the system SHALL accept the name

#### Scenario: Empty name is rejected
- **WHEN** an HR user submits an empty candidate name
- **THEN** the system SHALL display a validation error: "Le nom du candidat est obligatoire."

#### Scenario: Name exceeds maximum length
- **WHEN** an HR user submits a name longer than 255 characters
- **THEN** the system SHALL display a validation error indicating the maximum length

#### Scenario: Whitespace-only name is trimmed and rejected
- **WHEN** an HR user submits a name containing only spaces "   "
- **THEN** the `prepareForValidation()` method SHALL trim it to an empty string
- **AND** the required rule SHALL reject it

### Requirement: CV text is validated

The system SHALL require a non-empty CV text with a maximum length of 50,000 characters.

#### Scenario: Valid CV text passes
- **WHEN** an HR user submits CV text containing at least one character
- **THEN** the system SHALL accept the CV text

#### Scenario: Empty CV text is rejected
- **WHEN** an HR user submits an empty CV text
- **THEN** the system SHALL display a validation error: "Le texte du CV est obligatoire."

#### Scenario: CV text exceeds maximum length
- **WHEN** an HR user submits CV text longer than 50,000 characters
- **THEN** the system SHALL display a validation error indicating the maximum length

### Requirement: Job offer ID must reference an existing offer

The system SHALL validate that the submitted job offer ID exists in the database.

#### Scenario: Valid offer ID passes
- **WHEN** an HR user submits an offre_id that exists
- **THEN** the system SHALL accept the offre_id

#### Scenario: Non-existent offer ID is rejected
- **WHEN** an HR user submits an offre_id that does not exist
- **THEN** the system SHALL display a validation error: "L'offre d'emploi sélectionnée est invalide."

### Requirement: Duplicate candidate submission is detected

The system SHALL reject a candidate submission when a candidate with the same name has already been submitted for the same job offer.

#### Scenario: Duplicate name for same offer redirects with validation error
- **WHEN** an HR user submits a candidate named "Jean Dupont" for offer ID 1
- **AND** a candidate with the same name already exists for offer ID 1
- **THEN** the system SHALL redirect to `route('offres.show', $offre)` with a validation error
- **AND** the error message SHALL read: "Ce candidat a déjà été soumis pour cette offre."
- **AND** SHALL NOT create a duplicate CandidateAnalysis record

#### Scenario: Same name for different offer is accepted
- **WHEN** an HR user submits a candidate named "Jean Dupont" for offer ID 2
- **AND** a candidate with name "Jean Dupont" exists for offer ID 1 but not offer ID 2
- **THEN** the system SHALL accept the submission
- **AND** no duplicate error SHALL be raised

### Requirement: Form Request uses French error messages and attribute names

The system SHALL display all validation error messages and use French attribute names in the SubmitCandidateRequest.

#### Scenario: French error message for missing name
- **WHEN** the name field fails the required rule
- **THEN** the error message SHALL read: "Le nom du candidat est obligatoire."

#### Scenario: French error message for missing CV text
- **WHEN** the cv_text field fails the required rule
- **THEN** the error message SHALL read: "Le texte du CV est obligatoire."

#### Scenario: French error message for invalid offer
- **WHEN** the offre_id fails the exists rule
- **THEN** the error message SHALL read: "L'offre d'emploi sélectionnée est invalide."

#### Scenario: French attribute names are used in generic messages
- **WHEN** a field fails a rule and the generic `:attribute` placeholder is used
- **THEN** the attribute name SHALL be French: "nom du candidat", "texte du CV", "offre d'emploi"

### Requirement: Submission is gated by offer ownership

The system SHALL reject candidate submissions when the authenticated user does not own the target job offer.

#### Scenario: Own offer passes authorization
- **WHEN** an authenticated user submits a candidate for their own job offer
- **THEN** the system SHALL proceed with validation

#### Scenario: Another user's offer returns 403
- **WHEN** an authenticated user submits a candidate for a job offer belonging to another user
- **THEN** the system SHALL return a 403 Forbidden response
- **AND** SHALL NOT create a Candidate or CandidateAnalysis record
- **AND** SHALL NOT dispatch any queue job

### Requirement: Analysis is dispatched via queue after submission validation

The system SHALL dispatch a queue job to perform AI analysis after the form validation passes and the Candidate record is created.

#### Scenario: Queue job is dispatched after valid submission
- **WHEN** an HR user submits valid candidate data for a job offer
- **THEN** the system SHALL create a Candidate record
- **AND** SHALL dispatch a queue job (e.g., `AnalyseCvJob`) with the candidate ID and job offer ID
- **AND** SHALL return a success response to the user without waiting for the AI analysis to complete

#### Scenario: Flash message on successful submission
- **WHEN** an HR user submits valid candidate data
- **THEN** the system SHALL redirect to the offer detail page
- **AND** SHALL flash a success message: "Candidature soumise. L'analyse est en cours."

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

