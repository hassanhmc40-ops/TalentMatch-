## Context

The `Recommendation` enum has three values: `Convoquer` (success), `Attente` (warning), `Rejeter` (danger). Each maps to a variant on the `<x-badge>` component. This match logic is duplicated in `dashboard.blade.php` and `candidate-analyses/show.blade.php`. The badge itself is generic — it has no recommendation-specific icon or semantic structure.

The analysis detail page shows the recommendation in a small badge alongside the justification in a plain card. There is no visual hierarchy that communicates the severity or positivity of the recommendation at a glance.

## Goals / Non-Goals

**Goals:**
- Create a `<x-recommendation-badge>` that maps `Recommendation` enum values to `variant`, `label`, and `icon` in one place
- Create a `<x-recommendation-callout>` that renders a full-width, color-coded callout card with icon, heading, and justification
- Replace the inline recommendation section in `candidate-analyses/show.blade.php` with the callout component
- Replace the inline `<x-badge>` in `dashboard.blade.php` with `<x-recommendation-badge>`
- Add recommendation summary stats to the offer detail page showing counts per recommendation type
- All variants use existing Tailwind color system (success/green, warning/amber, danger/red)
- Include SVG icons for each recommendation type

**Non-Goals:**
- No changes to the Recommendation enum or its labels
- No new database queries or API changes
- No JavaScript behavior changes
- No changes to the badge component itself
- No changes to the table component or layout structure

## Decisions

1. **Anonymous component over class component** — The `<x-recommendation-badge>` and `<x-recommendation-callout>` components will be anonymous Blade components (PHP files in `app/View/Components/` only if computation is needed). Given the logic is a simple mapping from `?Recommendation` to variant/label/icon, an anonymous component in `resources/views/components/` with a `@php` block is sufficient.

2. **Icons as inline SVGs** — Each recommendation type gets a dedicated SVG icon:
   - `Convoquer` → checkmark-circle (green)
   - `Attente` → clock (amber)
   - `Rejeter` → x-circle (red)
   - Icons are rendered inline to avoid asset pipeline dependencies.

3. **Callout card over alert component** — The recommendation section on the detail page gets a full-width card with a left accent border (`border-l-4`), colored background (`bg-{variant}-50`), icon, heading, and justification. This is more prominent than a simple badge.

4. **Recommendation stats via existing data** — The offer detail page already passes analyses. The stats card counts by `recommendation` value and displays 3 small stat blocks. No extra query needed.

## Risks / Trade-offs

- [Component duplication risk] — If a third view needs a recommendation badge, the centralized component prevents further duplication. Without it, each view adds another copy of the match expression.
- [Over-engineering risk] — For 3 enum values with simple mapping, a dedicated component might seem like overkill. However, given the duplication already exists in 2 views and will likely grow, the investment pays off after the third usage.
- [Design consistency] — The callout card introduces a new visual pattern. It must align with the existing card component's spacing and typography.
