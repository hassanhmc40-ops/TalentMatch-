## Purpose

Define the conversation history capability for TalentMatch, allowing HR users to browse, search, and filter past conversations from the dashboard with full context retention.

## Requirements

### Requirement: Dashboard displays conversation history

The dashboard SHALL display a "Conversations récentes" section showing the authenticated user's most recent conversations linked to a candidate analysis. Conversations without a `candidate_analysis_id` SHALL be excluded.

#### Scenario: Conversations section is visible on dashboard
- **WHEN** the authenticated user visits the dashboard
- **THEN** a "Conversations récentes" section SHALL be displayed
- **AND** SHALL show the 10 most recent conversations ordered by `updated_at` descending

#### Scenario: Each conversation displays candidate and offer info
- **WHEN** a conversation row is rendered
- **THEN** it SHALL display: candidate name, offer title, conversation title, message count, truncated last message preview, and relative last activity date

#### Scenario: Empty state when no conversations exist
- **WHEN** the authenticated user has no conversations linked to candidate analyses
- **THEN** the section SHALL display an empty state message

#### Scenario: Conversations without candidate_analysis_id are hidden
- **WHEN** a conversation has a null `candidate_analysis_id`
- **THEN** it SHALL NOT appear in the conversations section

### Requirement: User can search conversations by candidate or offer

The user SHALL be able to filter the displayed conversations by candidate name or offer title using a search input.

#### Scenario: Filter by candidate name
- **WHEN** the user types a name in the search input
- **THEN** the list SHALL filter to show only conversations where the candidate name contains the search text (case-insensitive)

#### Scenario: Filter by offer title
- **WHEN** the user types an offer title in the search input
- **THEN** the list SHALL filter to show only conversations where the offer title contains the search text (case-insensitive)

#### Scenario: Combined search
- **WHEN** the user types text that matches a candidate name AND an offer title
- **THEN** conversations matching either criterion SHALL be shown

### Requirement: User can filter conversations by date range

The user SHALL be able to filter displayed conversations by a date range using from/to date inputs.

#### Scenario: Filter by start date
- **WHEN** the user selects a "from" date
- **THEN** only conversations with `updated_at` on or after that date SHALL be shown

#### Scenario: Filter by end date
- **WHEN** the user selects a "to" date
- **THEN** only conversations with `updated_at` on or before that date SHALL be shown

#### Scenario: Combined date range filter
- **WHEN** both "from" and "to" dates are set
- **THEN** only conversations within that date range SHALL be shown

### Requirement: Clicking a conversation opens chat with context

A conversation entry SHALL be clickable and navigate to the chat interface, preserving the candidate and offer context.

#### Scenario: Click navigates to chat
- **WHEN** the user clicks a conversation row
- **THEN** the application SHALL navigate to `conversations.show` for the corresponding offre and candidat
- **AND** the chat interface SHALL load the full conversation history
- **AND** the assistant SHALL have the candidate/offer/analysis context via the existing `setContext()` flow
