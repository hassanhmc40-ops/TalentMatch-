## Why

The current dashboard shows only 4 KPI cards and quick links. HR users need at-a-glance insight into score distribution across candidates and the ability to filter recent analyses by status. Adding a score distribution chart, status filter tabs, and trend indicators on KPI cards will make the dashboard a more effective monitoring and triage tool.

## What Changes

- Add a score distribution bar chart showing how many analyses fall into each score band (0-30, 31-60, 61-80, 81-100)
- Add status filter tabs (Tous, Terminés, En attente, Échoués) to the recent analyses section
- Add trend indicators (up/down/neutral) to each KPI card by comparing current values against cached snapshots
- Replace the two quick-link cards with a live recent analyses table filtered by status
- Add a "Recent analyses" table section showing the latest analyses from all offers with candidate name, offer title, score, recommendation, and status

## Capabilities

### New Capabilities
- `dashboard-enhancements`: Score distribution chart, status filter tabs, trend indicators, and recent analyses table for the dashboard

### Modified Capabilities
- `dashboard-layout`: KPI card requirements updated to include trend indicators with cached comparison snapshots; dashboard page layout updated with chart cards and filterable table sections

## Impact

- `app/Http/Controllers/DashboardController.php` — compute score distribution, recent analyses, and KPI trends (cached snapshots)
- `resources/views/dashboard.blade.php` — restructure layout with chart card, filter tabs, and recent analyses table
- `resources/views/components/kpi-card.blade.php` — trend prop already exists, no changes needed; wiring it with backend data
- No new database tables or migrations (all data exists in candidate_analyses)
