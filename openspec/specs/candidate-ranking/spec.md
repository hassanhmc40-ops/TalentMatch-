## Purpose

Define the ranked leaderboard display for candidates on the offer detail page, powered by a reusable Eloquent scope with deterministic tie-breaking.

## Requirements

### Requirement: CandidateAnalysis provides a ranked query scope

The system SHALL provide a reusable Eloquent scope on CandidateAnalysis that orders candidates by matching score descending with deterministic tie-breaking.

#### Scenario: scopeRanked orders by matching_score descending
- **WHEN** the `scopeRanked()` scope is applied to a CandidateAnalysis query
- **THEN** the results SHALL be ordered by `matching_score` descending

#### Scenario: Tie-breaking by years_experience
- **WHEN** two candidates have the same `matching_score`
- **THEN** the candidate with higher `years_experience` SHALL appear first

#### Scenario: Tie-breaking by skills count
- **WHEN** two candidates have the same `matching_score` and same `years_experience`
- **THEN** the candidate with more `extracted_skills` items SHALL appear first

#### Scenario: Tie-breaking by education level
- **WHEN** two candidates have the same `matching_score`, `years_experience`, and skills count
- **THEN** the candidate with the higher education level weight SHALL appear first
- **AND** education level weights SHALL use the same mapping as the comparison analysis tool: Doctorat (100), Bac+5 (80), Bac+3 (60), Bac+2 (40), Bac (20), others (0)

### Requirement: Offer detail page displays ranked candidate leaderboard

The offer detail page SHALL display analyzed candidates in a ranked leaderboard format with rank numbers and score progress bars.

#### Scenario: Candidates are displayed ordered by rank
- **WHEN** an authenticated owner views the offer detail page
- **AND** the offer has analyzed candidates
- **THEN** the candidates SHALL be displayed in descending order of `matching_score`
- **AND** each row SHALL show the candidate's rank number (1, 2, 3…) as the first column

#### Scenario: Score progress bar shows relative score
- **WHEN** an authenticated owner views the offer detail page
- **THEN** each candidate row SHALL include a score progress bar (0-100) showing the matching score visually
- **AND** the progress bar SHALL use a color gradient: green (≥70), yellow (40-69), red (<40)

#### Scenario: Empty offer shows no ranking
- **WHEN** an authenticated owner views an offer with no analyzed candidates
- **THEN** the system SHALL display "Aucun candidat analysé pour cette offre."
- **AND** no ranking rows SHALL be shown

### Requirement: N+1 queries are prevented on the ranked list

The system SHALL use eager loading to avoid N+1 queries when displaying the ranked leaderboard.

#### Scenario: Candidate analyses are eager-loaded with candidate data
- **WHEN** the ranked leaderboard is displayed
- **THEN** candidate analyses SHALL be eager-loaded with their associated candidate data
- **AND** the page SHALL NOT execute additional queries per candidate row

### Requirement: UI labels for rank are in French

The ranked leaderboard SHALL use French labels for all UI elements.

#### Scenario: Rank column header is French
- **WHEN** the ranked leaderboard is displayed
- **THEN** the rank column SHALL display "#" as the header
- **AND** "Classement" SHALL be used as the section title prefix if applicable
