## 1. Sidebar Component

- [x] 1.1 Create `resources/views/components/sidebar.blade.php` with fixed left positioning, navigation link array, and active state detection
- [x] 1.2 Add collapse/expand toggle with AlpineJS and localStorage persistence
- [x] 1.3 Add responsive behavior: overlay drawer on mobile (<768px) with backdrop
- [x] 1.4 Add navigation links: Tableau de bord, Mes offres, Candidats

## 2. Top Toolbar

- [x] 2.1 Create `resources/views/components/topbar.blade.php` with user name display
- [x] 2.2 Add user dropdown menu (Profil, Déconnexion)
- [x] 2.3 Add mobile hamburger toggle button for sidebar
- [x] 2.4 Ensure dropdown uses AlpineJS and existing Breeze dropdown pattern

## 3. KPI Card Component

- [x] 3.1 Create `resources/views/components/kpi-card.blade.php` with icon, value, label, color props
- [x] 3.2 Add color variant support: `primary`, `success`, `warning`, `danger`
- [x] 3.3 Add trend indicator (up/down arrow or neutral dot)

## 4. Dashboard Layout

- [x] 4.1 Update `layouts/app.blade.php` to use sidebar + topbar + content split
- [x] 4.2 Add responsive CSS structure: sidebar fixed left, content scrollable
- [x] 4.3 Ensure content area adjusts `ml-64` / `ml-16` / `ml-0` based on sidebar state

## 5. DashboardController

- [x] 5.1 Create `DashboardController` with `__invoke` method
- [x] 5.2 Add KPI queries: total offers, total analyzed candidates, average score, pending analyses
- [x] 5.3 Add cache layer (5-minute cache) for KPI queries
- [x] 5.4 Register route in `routes/web.php` — replace or update existing dashboard route

## 6. Dashboard View

- [x] 6.1 Update `dashboard.blade.php` with 4 KPI cards in a responsive grid
- [x] 6.2 Add empty state handling (zero values display as 0)
- [x] 6.3 Ensure page title pattern follows design system (`<h1>` + description)

## 7. Remove Old Navigation

- [x] 7.1 Remove `layouts/navigation.blade.php` or replace its content with sidebar inclusion
- [x] 7.2 Ensure no layout calls to the old navigation remain

## 8. Tests

- [x] 8.1 Write component test for `<x-sidebar>`: navigation links, active state, collapse toggle
- [x] 8.2 Write component test for `<x-kpi-card>`: renders icon, value, label, color variant
- [x] 8.3 Write feature test for dashboard page: KPI data displayed, scoped to authenticated user
- [x] 8.4 Write feature test for dashboard page: empty data shows zero values
