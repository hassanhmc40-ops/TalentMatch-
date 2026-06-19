## 1. Recommendation Badge Component

- [x] 1.1 Create `resources/views/components/recommendation-badge.blade.php` — accepts `recommendation` (Recommendation enum or null), maps to variant, label, and SVG icon using a @php block
- [x] 1.2 Component renders a colored badge with icon + label text inside the existing `<x-badge>` component

## 2. Recommendation Callout Component

- [x] 2.1 Create `resources/views/components/recommendation-callout.blade.php` — accepts `recommendation` (Recommendation enum) and `justification` (string)
- [x] 2.2 Component renders a full-width card with left accent border (`border-l-4`), tinted background (`bg-{variant}-50`), SVG icon, recommendation heading, and justification text

## 3. Update Analysis Detail Page

- [x] 3.1 Replace the inline `$recommendationVariant` match block with the new `<x-recommendation-badge>` and `<x-recommendation-callout>` components
- [x] 3.2 Remove the inline match block for recommendation variant from `candidate-analyses/show.blade.php`

## 4. Update Dashboard

- [x] 4.1 Replace the inline `$recVariant` match block in `dashboard.blade.php` with `<x-recommendation-badge>`
- [x] 4.2 Remove the inline match block for recommendation variant from `dashboard.blade.php`

## 5. Offer Detail — Recommendation Stats

- [x] 5.1 Add a recommendation summary stats card to `offres/show.blade.php` showing count of `convoquer`, `attente`, and `rejeter` analyses for the current offer
- [x] 5.2 Each stat block uses the corresponding variant color and icon

## 6. Test Coverage

- [x] 6.1 Write test: recommendation badge renders with correct variant, label, and icon for each enum value
- [x] 6.2 Write test: recommendation badge renders neutral variant when recommendation is null
- [x] 6.3 Write test: recommendation callout renders heading, icon, and justification text
- [x] 6.4 Write test: offer detail page shows recommendation stats card with correct counts

## 7. Code Quality & Archive

- [x] 7.1 Run `vendor/bin/pint` for code formatting
- [x] 7.2 Run full test suite: `php artisan test --compact`
- [x] 7.3 Archive the change
- [x] 7.4 Commit and push to repository
