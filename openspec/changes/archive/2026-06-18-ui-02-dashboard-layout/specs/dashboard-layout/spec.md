## ADDED Requirements

### Requirement: Sidebar navigation displays main navigation links

The system SHALL render a fixed sidebar on the left with navigation links grouped by section. The sidebar SHALL support a collapsed state (icons only) and an expanded state (icons + labels).

#### Scenario: Sidebar shows all navigation links
- **WHEN** a logged-in user views any authenticated page
- **THEN** the sidebar SHALL display links for: Tableau de bord, Mes offres, Candidats
- **AND** the currently active page SHALL be highlighted with the primary color

#### Scenario: Sidebar collapses and expands
- **WHEN** the user clicks the collapse toggle button
- **THEN** the sidebar SHALL collapse to show only icons (64px width)
- **WHEN** the user clicks again
- **THEN** the sidebar SHALL expand to full width (256px)

#### Scenario: Collapse state persists across page loads
- **WHEN** the user collapses the sidebar and navigates to another page
- **THEN** the sidebar SHALL remain collapsed

#### Scenario: Mobile sidebar is an overlay drawer
- **WHEN** the viewport is below 768px
- **THEN** the sidebar SHALL be hidden by default
- **AND** a hamburger button SHALL appear in the top bar
- **WHEN** the user clicks the hamburger button
- **THEN** the sidebar SHALL slide in as an overlay with a backdrop

### Requirement: Top toolbar shows user info and session controls

The system SHALL render a fixed top toolbar with the user's name, a user menu dropdown (profil, déconnexion), and a mobile menu toggle.

#### Scenario: Top toolbar displays user name
- **WHEN** a logged-in user views any authenticated page
- **THEN** the top toolbar SHALL show the authenticated user's name
- **AND** a dropdown menu with "Profil" and "Déconnexion" links

#### Scenario: Mobile menu toggle is visible on small screens
- **WHEN** the viewport is below 768px
- **THEN** a hamburger menu button SHALL appear in the top toolbar
- **WHEN** clicked, the sidebar overlay SHALL open

### Requirement: KPI cards display aggregated dashboard metrics

The dashboard homepage SHALL display 4 KPI cards showing key metrics: total job offers, total analyzed candidates, average matching score, and pending analyses count.

#### Scenario: KPI card renders icon, value, label, and trend
- **WHEN** the user visits the dashboard
- **THEN** each KPI card SHALL display a relevant icon, the numeric value, a descriptive label, and a color-coded trend indicator

#### Scenario: KPI data is computed from database
- **WHEN** the dashboard loads
- **THEN** the following metrics SHALL be shown:
  - Total job offers owned by the user
  - Total candidates analyzed across the user's offers
  - Average matching score across all analyses
  - Count of analyses with "attente" recommendation

#### Scenario: Empty state shows zero values
- **WHEN** the user has no offers or analyses
- **THEN** KPI cards SHALL display 0 with a neutral trend

### Requirement: Dashboard layout uses sidebar + content split

The main application layout SHALL use a sidebar + content area split, where the sidebar is fixed on the left and content fills the remaining space.

#### Scenario: Content area adjusts to sidebar state
- **WHEN** the sidebar is expanded
- **THEN** the content area SHALL use `ml-64` (or equivalent Tailwind spacing)
- **WHEN** the sidebar is collapsed
- **THEN** the content area SHALL use `ml-16`

#### Scenario: Layout is responsive
- **WHEN** the viewport is 768px or wider
- **THEN** the sidebar SHALL be persistently visible
- **WHEN** the viewport is below 768px
- **THEN** the sidebar SHALL be hidden with a slide-in overlay
- **AND** the content area SHALL use full width (`ml-0`)

### Requirement: KPI card is a reusable Blade component

A `<x-kpi-card>` Blade component SHALL render a single KPI metric with icon, value, label, and optional trend color.

#### Scenario: KPI card renders with given props
- **WHEN** rendering `<x-kpi-card icon="briefcase" :value="12" label="Offres d'emploi" color="primary" />`
- **THEN** it SHALL display a primary-colored icon, the number 12, and the label "Offres d'emploi"

#### Scenario: KPI card trend colors map to semantic meaning
- **WHEN** `color` is `success` — green accent
- **WHEN** `color` is `warning` — amber accent
- **WHEN** `color` is `danger` — red accent
- **WHEN** `color` is `primary` — blue accent
