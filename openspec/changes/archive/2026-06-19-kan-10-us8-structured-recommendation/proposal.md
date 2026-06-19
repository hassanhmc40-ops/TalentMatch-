## Why

US8 says: "HR user can clearly see the typed recommendation: À convoquer, En attente, À rejeter."

Currently, the recommendation is displayed as a small badge in both the dashboard and analysis detail page. The variant mapping (`convoquer → success`, `attente → warning`, `rejeter → danger`) is duplicated manually in two Blade views. The badge lacks an icon and the recommendation card on the detail page is a simple badge + justification — it doesn't visually communicate the decision at a glance.

Making the recommendation visually prominent — with dedicated icons, full-width color-coded callout cards, and screen-reader accessible labels — helps HR agents make faster triage decisions.

## What Changes

- Create a reusable `<x-recommendation-badge>` Blade component that encapsulates the `Recommendation` enum → variant/label/icon mapping (eliminates duplication)
- Replace the simple recommendation card on the analysis detail page with a prominent full-width callout card featuring an icon, the recommendation label as a heading, a color-coded background, and the justification text
- Update the dashboard to use the new `<x-recommendation-badge>` component instead of the inline match expression
- Add recommendation summary stats card showing how many analyses of each type exist (convoquer / attente / rejeter) — visible on the offer detail page (US4)

## Capabilities

### New Capabilities
- `recommendation-ux`: Reusable recommendation badge component, recommendation callout card, recommendation summary stats

### Modified Capabilities
- `candidate-analysis-view`: Replace inline recommendation section with recommendation callout card

## Impact

- `app/View/Components/RecommendationBadge.php` — new Blade component class
- `resources/views/components/recommendation-badge.blade.php` — new component view with icon + variant mapping
- `resources/views/components/recommendation-callout.blade.php` — new full-width callout card component
- `resources/views/candidate-analyses/show.blade.php` — replace inline recommendation card with recommendation callout
- `resources/views/dashboard.blade.php` — replace inline match with `<x-recommendation-badge>`
- `resources/views/offres/show.blade.php` — add recommendation summary stats card (if not already present)
- `tests/Feature/RecommendationComponentsTest.php` — new tests for badge and callout components
- `tests/Feature/CandidateAnalysisViewTest.php` — update recommendation assertions

No new database columns, enums, or migrations.
