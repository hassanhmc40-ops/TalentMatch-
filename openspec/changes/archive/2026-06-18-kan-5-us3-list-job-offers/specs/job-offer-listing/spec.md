## ADDED Requirements

### Requirement: HR user can view a paginated list of their job offers

The system SHALL display a paginated list of job offers belonging to the authenticated user, ordered by most recent first.

#### Scenario: Authenticated user sees their own offers
- **WHEN** an authenticated HR user navigates to `/offres`
- **THEN** the system SHALL display a list of job offers owned by that user
- **AND** the list SHALL NOT include offers belonging to other users
- **AND** the offers SHALL be ordered by creation date, most recent first

#### Scenario: List is paginated
- **WHEN** an authenticated HR user has more than 10 job offers
- **THEN** the system SHALL paginate the results at 10 items per page
- **AND** the system SHALL display pagination controls for navigating pages

#### Scenario: Unauthenticated user is redirected to login
- **WHEN** an unauthenticated user navigates to `/offres`
- **THEN** the system SHALL redirect to the login page

#### Scenario: User with no offers sees an empty state message
- **WHEN** an authenticated HR user with no job offers navigates to `/offres`
- **THEN** the system SHALL display a message indicating no offers have been created yet
- **AND** the system SHALL provide a link to the offer creation page

### Requirement: Each offer row displays key information

The system SHALL display the title, required skills, minimum experience, creation date, and candidate count for each job offer in the list.

#### Scenario: Offer row shows all details
- **WHEN** an authenticated HR user views the offer list
- **THEN** each offer row SHALL display: title (titre), a summary of required skills (compétences), minimum experience years (expérience min.), creation date (créé le), and number of analyzed candidates (candidats)

#### Scenario: Candidate count is zero initially
- **WHEN** a job offer has no candidate analyses
- **THEN** the candidate count SHALL display "0 candidat"

#### Scenario: Candidate count reflects number of analyzed candidates
- **WHEN** a job offer has 3 candidate analyses
- **THEN** the candidate count SHALL display "3 candidats"

### Requirement: List is scoped to the authenticated user

The system SHALL filter the job offer list to only include offers owned by the current authenticated user.

#### Scenario: Other users' offers are not visible
- **WHEN** User A navigates to `/offres`
- **AND** User B has created 5 job offers
- **THEN** User A SHALL NOT see User B's offers in their list

### Requirement: N+1 queries are prevented

The system SHALL use eager loading to avoid N+1 query problems when displaying the candidate count per offer.

#### Scenario: Candidate count uses withCount
- **WHEN** the offer list page is loaded
- **THEN** the system SHALL use `withCount('candidateAnalyses')` to load candidate counts
- **AND** the page SHALL execute exactly 2 queries: one for the offers with count, one for the pagination count

### Requirement: UI labels are in French

The system SHALL display all column headers, labels, and navigation elements in French.

#### Scenario: Column headers are in French
- **WHEN** the offer list page is displayed
- **THEN** column headers SHALL be: "Titre", "Compétences", "Exp. min.", "Créé le", "Candidats"

#### Scenario: Navigation uses French text
- **WHEN** the offer list page has pagination
- **THEN** pagination controls SHALL display French labels: "Précédent" and "Suivant"
