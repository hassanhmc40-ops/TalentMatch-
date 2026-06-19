## ADDED Requirements

### Requirement: Dedicated candidate analysis detail page

The system SHALL provide a dedicated analysis detail page at `GET /offres/{offre}/analyses/{analyse}` displaying the full structured AI evaluation report for a single candidate analysis.

#### Scenario: Analysis page renders all fields
- **WHEN** the HR user navigates to the analysis detail page for a completed analysis
- **THEN** the page SHALL display the candidate name, offer title, extracted skills, years of experience, education level, languages, matching score, strengths, gaps, missing skills, recommendation, and justification

#### Scenario: Analysis belongs to user's offer
- **WHEN** the analysis ID belongs to an offer that the authenticated user does not own
- **THEN** the system SHALL return a 404 response

#### Scenario: Analysis is pending
- **WHEN** the analysis `status` is `pending`
- **THEN** the page SHALL display "Analyse en cours..." with a loading indicator instead of analysis details

#### Scenario: Analysis failed
- **WHEN** the analysis `status` is `failed`
- **THEN** the page SHALL display "Analyse échouée" with a message to resubmit the candidate

### Requirement: Score visualization

The matching score SHALL be displayed as a color-coded progress bar with the score level label.

#### Scenario: Progress bar shows exact score
- **WHEN** the analysis `matching_score` is 75
- **THEN** the progress bar SHALL show 75% filled
- **AND** SHALL display "75%" text next to the bar

#### Scenario: Progress bar color matches score level
- **WHEN** `matching_score` is in `0-30` range
- **THEN** the progress bar variant SHALL be `danger`
- **WHEN** `matching_score` is in `31-60` range
- **THEN** the progress bar variant SHALL be `warning`
- **WHEN** `matching_score` is in `61-80` range
- **THEN** the progress bar variant SHALL be `primary`
- **WHEN** `matching_score` is in `81-100` range
- **THEN** the progress bar variant SHALL be `success`

#### Scenario: Score level label is displayed
- **WHEN** the analysis `matching_score` is 75
- **THEN** the page SHALL display the label "Bon" next to the progress bar

### Requirement: Recommendation display

The recommendation SHALL be displayed as a color-coded badge with the French label.

#### Scenario: Recommendation badge colors
- **WHEN** `recommendation` is `Convoquer`
- **THEN** the badge SHALL use the `success` variant with label "À convoquer"
- **WHEN** `recommendation` is `Attente`
- **THEN** the badge SHALL use the `warning` variant with label "En attente"
- **WHEN** `recommendation` is `Rejeter`
- **THEN** the badge SHALL use the `danger` variant with label "À rejeter"

### Requirement: Navigation from offer detail to analysis

The offer detail page SHALL provide a link to each candidate's analysis detail page.

#### Scenario: Link to analysis from candidate table row
- **WHEN** the HR user views the offer detail page
- **THEN** each candidate row SHALL include a "Voir l'analyse" link to `analyses.show`
- **AND** SHALL retain the existing "Assistant →" link to the conversation view

### Requirement: Breadcrumb navigation

The analysis detail page SHALL include breadcrumb navigation back to the offer.

#### Scenario: Breadcrumb shows offer context
- **WHEN** the HR user views the analysis detail page
- **THEN** a breadcrumb SHALL display: "Mes offres → {titre de l'offre} → Analyse de {nom du candidat}"
- **AND** each segment SHALL link to the corresponding page
