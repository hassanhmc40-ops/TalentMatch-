## MODIFIED Requirements

### Requirement: HR user can see analyzed candidates with scores and recommendations

The system SHALL display a list of analyzed candidates for the job offer, showing their name, matching score, and typed recommendation. Each candidate row SHALL include a checkbox for selecting candidates to compare, and a "Comparer" button SHALL appear when at least two candidates are selected.

#### Scenario: Analyzed candidates are listed
- **WHEN** an authenticated owner views the offer detail page
- **AND** the offer has analyzed candidates
- **THEN** the system SHALL display each candidate's name, matching score (0-100), and recommendation
- **AND** the recommendations SHALL be displayed in French: "À convoquer", "En attente", "À rejeter"
- **AND** the candidate names SHALL be links to their analysis detail page (US7)

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
