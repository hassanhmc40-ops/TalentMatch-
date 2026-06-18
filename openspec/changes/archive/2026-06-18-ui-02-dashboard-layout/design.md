## Context

TalentMatch currently uses Breeze's default single top-nav layout with a user dropdown. The navigation bar already shows "Tableau de bord" and "Mes offres" links and will need "Candidats", "Agent conversationnel", and potentially more entries. A sidebar provides better scalability and is standard for HR SaaS applications.

## Goals / Non-Goals

**Goals:**
- Sidebar navigation: collapsible, shows active page indicator, groups links logically
- Top toolbar: user menu, notification placeholder, responsive mobile toggle
- Dashboard page with 4 KPI cards (total offers, analyzed candidates, average score, pending analyses)
- Responsive: sidebar collapses to icons on tablet, full overlay drawer on mobile
- Reusable `<x-sidebar>`, `<x-topbar>`, `<x-kpi-card>` Blade components

**Non-Goals:**
- No backend data changes (KPI data is computed from existing models)
- No new database tables or migrations
- No dashboard charts or graphs (KPI cards only)
- No real-time or WebSocket updates

## Decisions

1. **Fixed sidebar + fluid content** — Sidebar is fixed-width (64px collapsed, 256px expanded) on desktop, slides in as overlay on mobile. Content area fills remaining width. Standard admin dashboard pattern familiar to HR users.

2. **AlpineJS for sidebar toggle** — `x-data` on sidebar with `collapsed` state, persisted in localStorage. No new JS framework needed. Consistent with existing AlpineJS usage in modals and dropdowns.

3. **Blade component for KPI cards** — `<x-kpi-card icon title value change color />` renders an icon, label, number, and trend indicator. Data is passed from the controller.

4. **DashboardController for KPI aggregation** — Lightweight controller method that queries aggregates (count, avg, etc.) from the database. Single query per KPI, cached for 5 minutes to avoid repeated counting.

5. **Sidebar links defined in a config array** — Navigation items defined in the sidebar component itself for simplicity. When new features are added, a new link entry is added to the array.

## Risks / Trade-offs

- [Risk] Sidebar takes horizontal space from content area → Mitigation: Collapsible on desktop, auto-hidden on mobile, content adjusts via CSS grid/flex.
- [Risk] KPI queries could slow down dashboard on large datasets → Mitigation: Cache results 5 minutes, keep queries simple (COUNT, AVG).
- [Trade-off] Navigation links hardcoded in Blade vs. database-driven — For current scope (few nav items), Blade array is simpler and faster. Can extract to a service later if needed.
