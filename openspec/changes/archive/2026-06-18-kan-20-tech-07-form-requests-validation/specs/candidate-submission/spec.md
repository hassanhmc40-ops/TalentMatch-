## ADDED Requirements

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

### Requirement: Job offer ID must reference an existing offer owned by the user

The system SHALL validate that the submitted job offer ID exists in the database and belongs to the authenticated user.

#### Scenario: Valid offer ID passes
- **WHEN** an HR user submits an offre_id that exists and belongs to them
- **THEN** the system SHALL accept the offre_id

#### Scenario: Non-existent offer ID is rejected
- **WHEN** an HR user submits an offre_id that does not exist
- **THEN** the system SHALL display a validation error: "L'offre d'emploi sélectionnée est invalide."

#### Scenario: Offer ID belonging to another user is rejected
- **WHEN** an HR user submits an offre_id that belongs to a different user
- **THEN** the system SHALL display a validation error indicating the offer is invalid

### Requirement: Duplicate candidate submission is detected

The system SHALL reject a candidate submission when a candidate with the same name has already been submitted for the same job offer.

#### Scenario: First submission is accepted
- **WHEN** an HR user submits a candidate named "Jean Dupont" for offer ID 1
- **THEN** the system SHALL accept the submission
- **AND** no duplicate error SHALL be raised

#### Scenario: Duplicate name for same offer is rejected
- **WHEN** an HR user submits a candidate named "Jean Dupont" for offer ID 1
- **AND** a candidate with the same name already exists for offer ID 1
- **THEN** the system SHALL display a validation error: "Ce candidat a déjà été soumis pour cette offre."

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
