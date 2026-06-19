## 1. Dashboard Controller

- [x] 1.1 Add score distribution query: count completed analyses grouped by score band (0-30, 31-60, 61-80, 81-100) with cache
- [x] 1.2 Add recent analyses query: latest 10 analyses across all user's offers with eager-loaded candidate and offer
- [x] 1.3 Add KPI trend computation: compare current KPI values against cached previous snapshot, store current as new snapshot
- [x] 1.4 Add status filter parameter support for recent analyses

## 2. Dashboard View

- [x] 2.1 Add score distribution chart card using CSS bars (4 bars with proportional heights and color coding)
- [x] 2.2 Add Alpine.js filter tabs (Tous, Terminés, En attente, Échoués) with `x-data` state management
- [x] 2.3 Add recent analyses table showing candidate name, offer title, score, recommendation badge, status badge, and action link
- [x] 2.4 Add KPI trend wiring: pass `trend` prop to `<x-kpi-card>` with computed values
- [x] 2.5 Replace static quick-link cards with the recent analyses section
- [x] 2.6 Add empty states for chart and table when no data exists

## 3. Test Coverage

- [x] 3.1 Write test: dashboard returns 200 and shows all KPI cards
- [x] 3.2 Write test: dashboard shows score distribution data
- [x] 3.3 Write test: dashboard shows recent analyses table
- [x] 3.4 Write test: KPI trends show neutral on first load
- [x] 3.5 Write test: empty state when user has no analyses

## 4. Code Quality & Spec Sync

- [x] 4.1 Run `vendor/bin/pint` for code formatting
- [x] 4.2 Run full test suite: `php artisan test --compact`
- [x] 4.3 Sync delta specs to main `dashboard-layout/spec.md`
- [x] 4.4 Create main spec `dashboard-enhancements/spec.md`

## 5. Archive & Push

- [x] 5.1 Archive the change
- [x] 5.2 Commit and push to repository
