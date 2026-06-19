## Purpose

HR users need to review the full criteria of a specific job offer and see which candidates have been analyzed with their matching scores and recommendations. This capability covers the job offer detail page that shows offer criteria alongside a table of analyzed candidates.

## Requirements

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

The system SHALL display a ranked list of analyzed candidates for the job offer, ordered by matching score descending, showing each candidate's rank, name, matching score (with progress bar), and typed recommendation. Each candidate row SHALL include a checkbox for selecting candidates to compare, and a "Comparer" button SHALL appear when at least two candidates are selected.

#### Scenario: Analyzed candidates are listed in ranked order
- **WHEN** an authenticated owner views the offer detail page
- **AND** the offer has analyzed candidates
- **THEN** the candidates SHALL be displayed in descending order of `matching_score`
- **AND** each row SHALL show the candidate's rank number, name (linked to analysis detail page), matching score (0-100) with a progress bar, and recommendation
- **AND** the recommendations SHALL be displayed in French: "À convoquer", "En attente", "À rejeter"
- **AND** the candidate names SHALL be links to their analysis detail page (US7)

#### Scenario: Tie-breaking maintains deterministic order
- **WHEN** two candidates have the same `matching_score`
- **THEN** the candidate with more `years_experience` SHALL appear first
- **AND** if still tied, the candidate with more `extracted_skills` SHALL appear first
- **AND** if still tied, the candidate with the higher education level weight SHALL appear first

#### Scenario: Candidate with no analyses shows empty table
- **WHEN** an authenticated owner views an offer with no candidate analyses
- **THEN** the system SHALL display a message indicating no candidates have been analyzed yet

#### Scenario: Candidate selection checkboxes
- **WHEN** the offer has at least one analyzed candidate
- **THEN** each candidate row SHALL include a checkbox input
- **AND** selecting a checkbox SHALL visually highlight the row

#### Scenario: Compare button appears with exactly 2 selections
- **WHEN** exactly two candidate checkboxes are selected
- **THEN** a "Comparer les candidats sélectionnés" button SHALL appear above the candidate table
- **AND** the button SHALL link to `/offres/{offre}/comparer?candidats[]=X&candidats[]=Y`

#### Scenario: Compare button is disabled with wrong selection count
- **WHEN** fewer than two or more than two candidate checkboxes are selected
- **THEN** the "Comparer les candidats sélectionnés" button SHALL be disabled
- **AND** SHALL show a tooltip or hint: "Sélectionnez exactement 2 candidats"

#### Scenario: Compare button hidden when no candidates
- **WHEN** the offer has zero analyzed candidates
- **THEN** no compare button SHALL be displayed

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
