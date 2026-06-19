## ADDED Requirements

### Requirement: Dedicated side-by-side comparison page

The system SHALL provide a dedicated comparison page at `GET /offres/{offre}/comparer?candidats[]=X&candidats[]=Y` that renders two candidates side-by-side for a given job offer.

#### Scenario: Comparison page shows both candidates
- **WHEN** the HR user navigates to `/offres/{offre}/comparer?candidats[]=1&candidats[]=2`
- **AND** both candidates belong to the same offer owned by the authenticated user
- **AND** both have completed analyses
- **THEN** the page SHALL display two equal-width columns, one per candidate
- **AND** each column SHALL show the candidate name, matching score progress bar, score level label, strengths list, gaps list, and recommendation badge

#### Scenario: Comparison is scoped to the authenticated user's offer
- **WHEN** the offer belongs to another user
- **THEN** the system SHALL return a 403 Forbidden response

#### Scenario: Non-existent offer returns 404
- **WHEN** any user navigates with an offer ID that does not exist
- **THEN** the system SHALL return a 404 Not Found response

#### Scenario: Candidates must belong to the same offer
- **WHEN** either candidate ID does not belong to the specified offer
- **THEN** the system SHALL display "Candidat non trouvé pour cette offre."

#### Scenario: Only one candidate provided
- **WHEN** less than two candidate IDs are provided
- **THEN** the system SHALL redirect back to the offer detail page with an error message "Sélectionnez deux candidats à comparer."

#### Scenario: More than two candidates provided
- **WHEN** more than two candidate IDs are provided
- **THEN** the system SHALL only compare the first two candidates and ignore the rest

#### Scenario: Candidate analysis is pending
- **WHEN** a candidate's analysis status is `pending`
- **THEN** the candidate's column SHALL display "Analyse en cours..." instead of comparison data

#### Scenario: Candidate analysis failed
- **WHEN** a candidate's analysis status is `failed`
- **THEN** the candidate's column SHALL display "Analyse échouée" with a message to resubmit

### Requirement: Score comparison with difference indicator

The comparison page SHALL display a prominent score difference indicator between the two candidates, with a progress bar for each candidate's matching score.

#### Scenario: Score difference is shown at the top
- **WHEN** both candidates have completed analyses
- **THEN** a banner at the top SHALL display "Écart de score: X points" with the absolute difference
- **AND** the higher-scoring candidate SHALL be highlighted

#### Scenario: Score progress bars are color-coded
- **WHEN** candidate A has `matching_score` of 75 and candidate B has 45
- **THEN** candidate A's progress bar SHALL be 75% filled with `primary` variant
- **AND** candidate B's progress bar SHALL be 45% filled with `warning` variant
- **AND** the color SHALL follow the same range rules as the analysis view (0-30 danger, 31-60 warning, 61-80 primary, 81-100 success)

### Requirement: Strengths and gaps are listed per candidate

Each candidate column SHALL display a list of strengths and gaps extracted from the analysis.

#### Scenario: Strengths are listed with positive icon
- **WHEN** a candidate has strengths in their analysis
- **THEN** each strength SHALL be displayed as a bullet point with a checkmark icon
- **AND** a "Points forts" heading SHALL precede the list

#### Scenario: Gaps are listed with warning icon
- **WHEN** a candidate has gaps in their analysis
- **THEN** each gap SHALL be displayed as a bullet point with a warning icon
- **AND** a "Lacunes" heading SHALL precede the list

#### Scenario: Empty strengths or gaps
- **WHEN** a candidate has no strengths or gaps
- **THEN** the corresponding section SHALL display "Aucun"

### Requirement: Navigation from comparison page

The comparison page SHALL provide navigation back to the offer detail page and links to each candidate's individual analysis.

#### Scenario: Back to offer link
- **WHEN** viewing the comparison page
- **THEN** a breadcrumb or link SHALL allow returning to the offer detail page

#### Scenario: Individual analysis links
- **WHEN** viewing the comparison page
- **THEN** each candidate's name SHALL link to their individual analysis detail page
