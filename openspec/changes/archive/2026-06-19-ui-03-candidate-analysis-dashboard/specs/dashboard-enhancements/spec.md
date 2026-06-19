## ADDED Requirements

### Requirement: Score distribution bar chart

The dashboard SHALL display a bar chart showing the distribution of matching scores across all completed candidate analyses, grouped into 4 score bands.

#### Scenario: Chart shows counts per score band
- **WHEN** the user visits the dashboard
- **THEN** the chart SHALL display 4 vertical bars representing score bands: `0-30` (Faible), `31-60` (Moyen), `61-80` (Bon), `81-100` (Excellent)
- **AND** each bar SHALL show the count of analyses in that band
- **AND** each bar SHALL have a height proportional to the count relative to the maximum band count

#### Scenario: Bar colors match score level
- **WHEN** the score band is `0-30`
- **THEN** the bar SHALL use the `danger` color
- **WHEN** the score band is `31-60`
- **THEN** the bar SHALL use the `warning` color
- **WHEN** the score band is `61-80`
- **THEN** the bar SHALL use the `primary` color
- **WHEN** the score band is `81-100`
- **THEN** the bar SHALL use the `success` color

#### Scenario: Empty state
- **WHEN** there are no completed analyses
- **THEN** the chart area SHALL display "Aucune analyse disponible" instead of bars

### Requirement: Status filter tabs for recent analyses

The dashboard SHALL display filter tabs to switch between subsets of recent candidate analyses by status.

#### Scenario: Filter tabs show all status options
- **WHEN** the user visits the dashboard
- **THEN** the filter tabs SHALL display: "Tous", "Terminés", "En attente", "Échoués"
- **AND** the "Tous" tab SHALL be active by default

#### Scenario: Clicking a tab filters the list
- **WHEN** the user clicks "Terminés"
- **THEN** only analyses with `status = 'completed'` SHALL be shown
- **WHEN** the user clicks "En attente"
- **THEN** only analyses with `status = 'pending'` SHALL be shown
- **WHEN** the user clicks "Échoués"
- **THEN** only analyses with `status = 'failed'` SHALL be shown
- **WHEN** the user clicks "Tous"
- **THEN** all analyses SHALL be shown regardless of status

### Requirement: Recent analyses table

The dashboard SHALL display a table of the most recent candidate analyses across all user's offers.

#### Scenario: Table shows latest 10 analyses
- **WHEN** the user visits the dashboard
- **THEN** a "Analyses récentes" table SHALL display the 10 most recent analyses ordered by `created_at` descending
- **AND** each row SHALL show: candidate name, offer title, matching score (with color), recommendation badge, status badge, and a link to the analysis detail page

#### Scenario: Table respects status filter
- **WHEN** a status filter tab is active
- **THEN** the table SHALL only display analyses matching the selected status
- **AND** the count SHALL reflect the filtered total

#### Scenario: Empty state
- **WHEN** there are no analyses matching the selected filter
- **THEN** the table SHALL display "Aucune analyse trouvée"
