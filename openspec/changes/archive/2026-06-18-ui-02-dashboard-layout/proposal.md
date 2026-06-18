## Why

TalentMatch's current layout uses Breeze's default top navigation bar, which will become overcrowded as more features are added (offers CRUD, candidate analysis, conversational agent, user profile). A professional HR SaaS dashboard layout with sidebar navigation, header toolbar, and KPI cards is needed to scale the UI without clutter.

## What Changes

- Replace the single top-nav layout with a sidebar + top bar hybrid layout
- Add a dashboard homepage with KPI cards (total offers, active analyses, average score, recent submissions)
- Implement responsive behavior: sidebar collapses on mobile, top bar shows mobile menu toggle
- Update the existing `layouts/app.blade.php` and `layouts/navigation.blade.php`
- Update `dashboard.blade.php` with meaningful KPI widgets

## Capabilities

### New Capabilities
- `dashboard-layout`: Sidebar navigation, top toolbar, KPI cards, responsive layout architecture

### Modified Capabilities
- (none — this is a new capability)

## Impact

- `resources/views/layouts/app.blade.php` — rebuilt to sidebar + top bar structure
- `resources/views/layouts/navigation.blade.php` — replaced by sidebar component
- `resources/views/dashboard.blade.php` — KPI cards and data aggregation
- `app/Http/Controllers/DashboardController.php` — added KPI data method
- `routes/web.php` — dashboard route may be updated
- No changes to database, models, or AI layer
