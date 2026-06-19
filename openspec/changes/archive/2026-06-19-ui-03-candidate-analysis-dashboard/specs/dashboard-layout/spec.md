## MODIFIED Requirements

### Requirement: KPI cards display aggregated dashboard metrics

The dashboard homepage SHALL display 4 KPI cards showing key metrics: total job offers, total analyzed candidates, average matching score, and pending analyses count.

#### Scenario: KPI card renders icon, value, label, and trend
- **WHEN** the user visits the dashboard
- **THEN** each KPI card SHALL display a relevant icon, the numeric value, a descriptive label, and a color-coded trend indicator

#### Scenario: Trend is computed from cached snapshot
- **WHEN** the dashboard loads
- **THEN** the trend indicator SHALL compare the current metric value against the previously cached value
- **AND** SHALL show `up` when the current value is higher
- **AND** SHALL show `down` when the current value is lower
- **AND** SHALL show `neutral` when the value is unchanged or no previous snapshot exists

#### Scenario: Previous snapshot is updated after comparison
- **WHEN** the dashboard loads and trends are computed
- **THEN** the current metric values SHALL be saved as the new snapshot for the next comparison
- **AND** the snapshot SHALL have a 10-minute TTL
