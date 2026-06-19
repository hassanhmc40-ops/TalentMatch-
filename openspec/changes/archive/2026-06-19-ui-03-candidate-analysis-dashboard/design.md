## Context

The current dashboard shows 4 KPI cards (total offers, analyzed candidates, avg score, pending analyses) and two quick-link cards. KPI data is cached for 5 minutes. The `kpi-card` component already has a `trend` prop (`up`/`down`/`neutral`) with SVG icons, but it is never wired — it always passes `trend='neutral'`. There is no score visualization, status filtering, or recent activity feed on the dashboard.

## Goals / Non-Goals

**Goals:**
- Add a CSS-based score distribution bar chart showing analysis counts per score band
- Add Alpine.js-powered status filter tabs to filter a recent analyses list
- Wire KPI card trend indicators by comparing current values with cached snapshots
- Replace static quick-link cards with a live "Recent analyses" table section
- Keep all data computed server-side in the existing `DashboardController`

**Non-Goals:**
- No JavaScript charting library (CSS/HTML bars only)
- No new database columns or migration (all data exists)
- No historical trend tracking beyond the last cached snapshot
- No real-time updates (data refreshes on page load)
- No changes to the layout structure (sidebar, topbar, app layout unchanged)

## Decisions

1. **CSS bar chart over JS chart library** — The score distribution has only 4 bands with simple counts. A `<div>` with `style="height: {pct}%"` and Tailwind color classes is sufficient. Avoids npm dependency and build complexity. Alternative (Chart.js, Alpine-Chart) was rejected as overkill for 4 bars.

2. **Alpine.js for filter tabs** — The existing layout already uses Alpine.js (`x-data`, `x-show`). Adding filter state (`activeTab`) with `x-data="{ tab: 'all' }"` is minimal and consistent. The tabs control which subset of the recent analyses list is displayed.

3. **KPI trend via cached snapshot comparison** — Store the previous KPI values in a separate cache key (`dashboard.kpi.previous.{userId}`) alongside the current values. On each dashboard load, compare current vs previous to determine trend direction, then update the snapshot. First load always shows `neutral`. This avoids a separate DB query or historical table.

4. **KPI cache extended to 10 minutes** — The trend comparison needs two data points. Using a separate 10-minute cache for the previous snapshot ensures trends don't reset too frequently while keeping the dashboard responsive.

5. **Recent analyses table replaces quick-link cards** — The two quick-link cards at the bottom provide low value compared to showing actual analysis data. Replacing them with a compact table (candidate name, offer title, score, recommendation, status, date) gives actionable insight at a glance. Links to offers and analysis detail pages remain accessible via the sidebar navigation.

## Risks / Trade-offs

- [Trend accuracy] → Trends compare against a single cached snapshot that could be stale. Mitigation: reset the snapshot when the user has no dashboard activity for 10 minutes (cache TTL).
- [Performance] → The controller now runs 5 DB queries instead of 4 (added score distribution + recent analyses). Mitigation: the entire block is still cached for 5 minutes.
- [Score chart empty state] → If there are zero analyses, all bars show 0 height. The chart section should display a "Aucune analyse" message instead of empty bars.
