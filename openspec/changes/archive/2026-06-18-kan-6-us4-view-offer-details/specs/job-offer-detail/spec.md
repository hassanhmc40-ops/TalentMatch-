## ADDED Requirements

### Requirement: HR user can view a single job offer's criteria

The system SHALL display the full criteria of a job offer when the authenticated owner navigates to its detail page.

#### Scenario: Authenticated owner sees offer criteria
- **WHEN** an authenticated HR user navigates to `/offres/{id}`
- **AND** the job offer belongs to that user
- **THEN** the system SHALL display the offer title (titre), description (description), required skills (compétences requises), and minimum experience (années d'expérience minimum)

#### Scenario: Unauthenticated user is redirected to login
- **WHEN** an unauthenticated user navigates to `/offres/{id}`
- **THEN** the system SHALL redirect to the login page

#### Scenario: Non-owner is forbidden
- **WHEN** User A navigates to `/offres/{id}` for a job offer owned by User B
- **THEN** the system SHALL return a 403 Forbidden response

#### Scenario: Non-existent offer returns 404
- **WHEN** any user navigates to `/offres/{id}` with an ID that does not exist
- **THEN** the system SHALL return a 404 Not Found response

### Requirement: HR user can see analyzed candidates with scores and recommendations

The system SHALL display a list of analyzed candidates for the job offer, showing their name, matching score, and typed recommendation.

#### Scenario: Analyzed candidates are listed
- **WHEN** an authenticated owner views the offer detail page
- **AND** the offer has analyzed candidates
- **THEN** the system SHALL display each candidate's name, matching score (0-100), and recommendation
- **AND** the recommendations SHALL be displayed in French: "À convoquer", "En attente", "À rejeter"
- **AND** the candidate names SHALL be links to their analysis detail page (US7)

#### Scenario: Candidate with no analyses shows empty table
- **WHEN** an authenticated owner views an offer with no candidate analyses
- **THEN** the system SHALL display a message indicating no candidates have been analyzed yet

### Requirement: CandidateAnalysis model stores structured analysis data

The system SHALL persist candidate analysis data with typed fields corresponding to the AI structured output contract.

#### Scenario: Analysis stores all structured fields
- **WHEN** a candidate analysis is created
- **THEN** it SHALL store: extracted skills (array), years of experience (integer), education level (string), languages (array), matching score (integer 0-100), strengths (array), gaps (array), missing skills (array), recommendation (enum), justification (string)

#### Scenario: Matching score is constrained to 0-100
- **WHEN** a matching score is saved
- **THEN** the database SHALL enforce an integer between 0 and 100

### Requirement: Recommendation uses a PHP backed enum

The system SHALL define a `Recommendation` backed enum with three cases: `Convoquer`, `Attente`, `Rejeter`.

#### Scenario: Enum cases are accessible
- **WHEN** accessing the Recommendation enum
- **THEN** `Recommendation::Convoquer` SHALL return `"convoquer"`
- **AND** `Recommendation::Attente` SHALL return `"attente"`
- **AND** `Recommendation::Rejeter` SHALL return `"rejeter"`

#### Scenario: Enum provides French label
- **WHEN** calling a label method on the enum
- **THEN** `Convoquer` SHALL display as "À convoquer"
- **AND** `Attente` SHALL display as "En attente"
- **AND** `Rejeter` SHALL display as "À rejeter"

### Requirement: N+1 queries are prevented on the detail page

The system SHALL use eager loading to avoid N+1 queries when displaying the candidate list with scores.

#### Scenario: Candidates eager-loaded with analyses
- **WHEN** the offer detail page is loaded
- **THEN** the system SHALL eager-load candidate analyses with their associated candidate data
- **AND** the page SHALL NOT execute additional queries per candidate row in the table

### Requirement: UI labels are in French

The system SHALL display all labels and messages on the offer detail page in French.

#### Scenario: Field labels are French
- **WHEN** the offer detail page is displayed
- **THEN** the offer criteria section SHALL use French labels: "Titre", "Description", "Compétences requises", "Années d'expérience minimum"
- **AND** the candidate table SHALL use French headers: "Candidat", "Score", "Recommandation"

#### Scenario: Empty state message is French
- **WHEN** an offer has no analyzed candidates
- **THEN** the system SHALL display: "Aucun candidat analysé pour cette offre."
