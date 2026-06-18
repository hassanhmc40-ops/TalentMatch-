## Context

TalentMatch uses Tailwind CSS v3 with default Breeze styling. The current `tailwind.config.js` only extends `fontFamily` (Figtree). All views rely on Breeze's utility classes directly, with no custom design tokens or component abstractions. As the app grows with offers CRUD, candidate analysis, and conversational AI views, a consistent design system is needed.

## Goals / Non-Goals

**Goals:**
- Define a complete set of design tokens (colors, typography, spacing, shadows, border radius) in `tailwind.config.js`
- Build reusable Blade components for all UI primitives (buttons, inputs, cards, tables, modals, badges, alerts, progress bars)
- Document UX rules: form layouts, navigation patterns, data display conventions
- Add accessibility requirements: focus indicators, color contrast, ARIA attributes, keyboard navigation
- Apply the design system to all existing views

**Non-Goals:**
- No backend logic or database changes
- No new routes or controllers
- No color theme switching (light theme only)
- No JavaScript framework migration (AlpineJS continues as-is)

## Decisions

1. **Tailwind CSS v3 `@apply` in Blade components over CSS files** — Extract utility patterns into Blade component classes using `@apply` in a `app.css` `@layer components` block. This keeps component styles co-located and avoids a separate CSS architecture.

2. **Breeze components replaced by custom components** — Wrap Breeze components (`<x-primary-button>`, `<x-text-input>`) with new design system components that accept variant props. This is backward-compatible: old Breeze component names can continue working as aliases.

3. **Custom `tailwind.config.js` palette** — Professional blue-grey primary (`#1e40af` → `#dbeafe` scale), teal accent, red/green/yellow semantic colors, with explicit 50–900 shades. No CSS custom properties (light-theme only).

4. **Component variants via `$attributes->merge()`** — Use Laravel Blade `$attributes->merge()` for variants (size, color, intent) instead of separate component files per variant. E.g., `<x-button variant="outline" size="sm">`.

5. **AlpineJS for interactive components** — Modals and dropdowns use AlpineJS `x-show`/`x-transition` (already present via Breeze). No new JS dependencies.

## Risks / Trade-offs

- [Risk] Over-engineering components before knowing exact usage patterns → Mitigation: Build only the 8 listed components as documented primitives; extend later as needed.
- [Risk] Styling regression on existing views → Mitigation: Apply design system to one view group at a time (auth → offers → candidates → profile), verifying visually after each pass.
- [Trade-off] Using `@apply` over Tailwind utility classes in templates — Some teams prefer raw utilities for consistency. We choose `@apply` in components to reduce repetition across 20+ views.
