## MODIFIED Requirements

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
