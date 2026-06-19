## ADDED Requirements

### Requirement: Reusable recommendation badge component

The system SHALL provide a `<x-recommendation-badge>` Blade component that renders a color-coded badge with an icon and the French label for a given recommendation value.

#### Scenario: Badge renders correct variant, icon, and label per recommendation
- **WHEN** `recommendation` is `Convoquer`
- **THEN** the component SHALL render a `success`-variant badge containing a checkmark-circle SVG icon and the text "À convoquer"
- **WHEN** `recommendation` is `Attente`
- **THEN** the component SHALL render a `warning`-variant badge containing a clock SVG icon and the text "En attente"
- **WHEN** `recommendation` is `Rejeter`
- **THEN** the component SHALL render a `danger`-variant badge containing an x-circle SVG icon and the text "À rejeter"

#### Scenario: Badge handles null recommendation
- **WHEN** `recommendation` is `null`
- **THEN** the component SHALL render a `neutral`-variant badge with the text "Non définie"

### Requirement: Reusable recommendation callout component

The system SHALL provide a `<x-recommendation-callout>` Blade component that renders a full-width, color-coded callout card with the recommendation heading, icon, and justification.

#### Scenario: Callout renders with color-coded accent and background
- **WHEN** `recommendation` is `Convoquer`
- **THEN** the callout SHALL have a green left accent border (`border-l-success-500`), a green-tinted background (`bg-success-50`), a checkmark-circle icon, and the heading "À convoquer"
- **WHEN** `recommendation` is `Attente`
- **THEN** the callout SHALL have an amber left accent border (`border-l-warning-500`), an amber-tinted background (`bg-warning-50`), a clock icon, and the heading "En attente"
- **WHEN** `recommendation` is `Rejeter`
- **THEN** the callout SHALL have a red left accent border (`border-l-danger-500`), a red-tinted background (`bg-danger-50`), an x-circle icon, and the heading "À rejeter"

#### Scenario: Callout displays justification text
- **WHEN** the callout is rendered with a non-empty `justification` string
- **THEN** the justification SHALL appear below the heading in neutral text color

### Requirement: Recommendation summary stats on offer detail page

The offer detail page SHALL display a summary card showing the count of analyses grouped by recommendation type.

#### Scenario: Stats card shows three recommendation counts
- **WHEN** the HR user views the offer detail page
- **THEN** the stats card SHALL display three stat blocks:
  - "À convoquer" with a green icon and the count of `Convoquer` analyses
  - "En attente" with an amber icon and the count of `Attente` analyses
  - "À rejeter" with a red icon and the count of `Rejeter` analyses

#### Scenario: Stats card handles zero counts
- **WHEN** there are no analyses with a given recommendation
- **THEN** the corresponding stat block SHALL display `0`
