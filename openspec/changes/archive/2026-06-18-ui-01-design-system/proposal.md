## Why

TalentMatch currently has no consistent visual identity. UI components are unstyled or rely on Breeze defaults, making the app look unfinished for HR agent users. A professional, cohesive design system is needed to establish credibility, reduce UI development time, and ensure a consistent HR SaaS experience across all pages.

## What Changes

- Define design tokens (colors, typography, spacing, shadows, border radius)
- Build a reusable component catalog (buttons, inputs, cards, tables, modals, badges, alerts, progress bars)
- Document UX rules for form layouts, navigation, and data display
- Add accessibility requirements for all components
- Apply the system across existing views (auth, offers, candidates)

## Capabilities

### New Capabilities
- `design-system`: Design tokens, component catalog, UX rules, accessibility requirements for TalentMatch's HR SaaS UI

### Modified Capabilities
- (none — this is a new capability, not a requirement-level change to existing ones)

## Impact

- All Blade views will use new component classes and design tokens
- Tailwind config will be extended with custom colors, fonts, spacing
- No changes to backend logic, database, or routes
- Existing Breeze pages will be restyled to match the design system
