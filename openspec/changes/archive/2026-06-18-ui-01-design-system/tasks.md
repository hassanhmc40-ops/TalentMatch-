## 1. Design Tokens

- [x] 1.1 Add custom color palette (blue-grey primary, teal accent, semantic colors) to `tailwind.config.js` `theme.extend.colors`
- [x] 1.2 Extend typography scale with heading tokens (`h1`–`h4`) and line heights in `tailwind.config.js`
- [x] 1.3 Extend border radius with `xs`–`xl` values and box shadow presets (`card`, `dropdown`, `modal`) in `tailwind.config.js`

## 2. Component: Button

- [x] 2.1 Create `resources/views/components/button.blade.php` with default `type="submit"`, primary styling
- [x] 2.2 Add variant support: `primary`, `secondary`, `outline`, `danger`, `ghost` via `$attributes->merge()`
- [x] 2.3 Add size support: `sm`, `md`, `lg` with corresponding padding and font size
- [x] 2.4 Ensure disabled state and additional class merging work

## 3. Component: Input

- [x] 3.1 Create `resources/views/components/input.blade.php` with label, input, and error/help text slots
- [x] 3.2 Add validation error styling (red border, error message below input)
- [x] 3.3 Add help text support with muted styling
- [x] 3.4 Ensure `type`, `name`, `id` attributes propagate correctly

## 4. Component: Card

- [x] 4.1 Create `resources/views/components/card.blade.php` with white background, shadow, rounded corners, padding
- [x] 4.2 Add optional `header` and `footer` slots with separator borders
- [x] 4.3 Ensure no top separator when header slot is absent

## 5. Component: Table

- [x] 5.1 Create `resources/views/components/table.blade.php` with responsive wrapper and HTML table structure
- [x] 5.2 Add `:headers` prop for `<thead>` and `:rows` prop for `<tbody>`
- [x] 5.3 Add action column support (right-aligned, slot-based)
- [x] 5.4 Add empty state with centered "Aucun résultat" message

## 6. Component: Modal

- [x] 6.1 Create `resources/views/components/modal.blade.php` with AlpineJS overlay and centered dialog
- [x] 6.2 Add backdrop click-to-close behavior
- [x] 6.3 Add `role="dialog"`, `aria-modal="true"`, `aria-labelledby` attributes

## 7. Component: Badge

- [x] 7.1 Create `resources/views/components/badge.blade.php` with variants: `success`, `warning`, `danger`, `info`, `neutral`
- [x] 7.2 Map each variant to corresponding semantic color

## 8. Component: Alert

- [x] 8.1 Create `resources/views/components/alert.blade.php` with type-based styling and semantic icon
- [x] 8.2 Add AlpineJS dismissible behavior via close button
- [x] 8.3 Add `role="alert"` attribute

## 9. Component: Progress Bar

- [x] 9.1 Create `resources/views/components/progress.blade.php` with full-width track and animated fill bar
- [x] 9.2 Add `:value` prop for percentage (0–100)
- [x] 9.3 Add color variant support (`success`, `warning`, `danger`)
- [x] 9.4 Add optional `label` prop

## 10. Apply to Auth Views

- [x] 10.1 Update `login.blade.php` to use `<x-card>`, `<x-input>`, `<x-button>` with proper spacing
- [x] 10.2 Update `register.blade.php` with design system components
- [x] 10.3 Update `forgot-password.blade.php` and `reset-password.blade.php`
- [x] 10.4 Update `verify-email.blade.php` and `confirm-password.blade.php`

## 11. Apply to Offer Views

- [x] 11.1 Update `offres/index.blade.php` with `<x-card>`, `<x-table>`, `<x-button>`
- [x] 11.2 Update `offres/create.blade.php` with `<x-card>`, `<x-input>`, `<x-button>`
- [x] 11.3 Update `offres/show.blade.php` with `<x-card>`, `<x-table>`, `<x-badge>`, `<x-button>`
- [x] 11.4 Update `offres/submit-candidate.blade.php` with `<x-card>`, `<x-input>`, `<x-alert>`, `<x-button>`

## 12. Apply to Profile Views

- [x] 12.1 Update profile edit form with `<x-card>`, `<x-input>`, `<x-alert>`, `<x-button>`
- [x] 12.2 Update delete-user form with `<x-card>`, `<x-alert>`, `<x-button variant="danger">`

## 13. Accessibility & UX

- [x] 13.1 Add `@layer components` CSS block in `app.css` for design system utility patterns
- [x] 13.2 Ensure focus-visible ring styles are applied globally
- [x] 13.3 Verify WCAG AA contrast for all color combinations
- [x] 13.4 Apply consistent page title pattern (`<h1>` + description) across all views
- [x] 13.5 Ensure vertical form layout (labels above inputs, right-aligned buttons) across all forms

## 14. Tests

- [x] 14.1 Write Blade component tests for `<x-button>` variants, sizes, disabled state
- [x] 14.2 Write component tests for `<x-input>` with label, error, help text
- [x] 14.3 Write component tests for `<x-card>` with/without header/footer
- [x] 14.4 Write component tests for `<x-table>` with data, actions column, empty state
- [x] 14.5 Write component tests for `<x-modal>` visibility and backdrop behavior
- [x] 14.6 Write component tests for `<x-badge>` variants
- [x] 14.7 Write component tests for `<x-alert>` types and dismissible behavior
- [x] 14.8 Write component tests for `<x-progress>` percentage, variant, label
