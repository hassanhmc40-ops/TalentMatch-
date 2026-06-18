## ADDED Requirements

### Requirement: Design tokens are defined in Tailwind config

The system SHALL define custom design tokens in `tailwind.config.js` under `theme.extend`, covering colors, typography, spacing, shadows, and border radius for a professional HR SaaS appearance.

#### Scenario: Color palette covers primary, accent, and semantic colors
- **WHEN** inspecting `tailwind.config.js`
- **THEN** it SHALL define a blue-grey primary scale (50–900)
- **AND** a teal accent scale (50–900)
- **AND** semantic colors for success (green), warning (yellow), danger (red), and info (blue)

#### Scenario: Typography scale is configured
- **WHEN** inspecting `tailwind.config.js`
- **THEN** it SHALL extend font sizes for headings (`h1`–`h4`) with appropriate sizes and line heights
- **AND** SHALL set the sans-serif font family to Figtree (already configured via Breeze)

#### Scenario: Spacing and border radius tokens are set
- **WHEN** inspecting `tailwind.config.js`
- **THEN** it SHALL extend border radius with `xs`, `sm`, `md`, `lg`, `xl` values
- **AND** SHALL extend box shadow with `card`, `dropdown`, `modal` presets
- **AND** SHALL keep default Tailwind spacing scale unchanged

### Requirement: Button component supports variants and sizes

A reusable Blade component `<x-button>` SHALL render styled buttons with support for variants (primary, secondary, outline, danger, ghost) and sizes (sm, md, lg).

#### Scenario: Default button uses primary variant
- **WHEN** rendering `<x-button>Submit</x-button>` without variant
- **THEN** it SHALL render a `<button>` with the primary color scheme (blue-grey background, white text)
- **AND** SHALL include `type="submit"` as default

#### Scenario: Variants produce distinct visual styles
- **WHEN** rendering `<x-button variant="danger">Supprimer</x-button>`
- **THEN** it SHALL render a red-toned button
- **WHEN** rendering `<x-button variant="outline">Annuler</x-button>`
- **THEN** it SHALL render a bordered button with transparent background
- **WHEN** rendering `<x-button variant="ghost">Lien</x-button>`
- **THEN** it SHALL render a text-only button without border or background

#### Scenario: Size prop adjusts padding and font size
- **WHEN** rendering `<x-button size="sm">Petit</x-button>`
- **THEN** it SHALL have reduced padding and smaller font
- **WHEN** rendering `<x-button size="lg">Grand</x-button>`
- **THEN** it SHALL have increased padding and larger font

#### Scenario: Button passes additional attributes
- **WHEN** rendering `<x-button disabled class="w-full">Valider</x-button>`
- **THEN** the `disabled` attribute and `w-full` class SHALL be present on the rendered `<button>`

### Requirement: Input component supports validation states

A reusable Blade component `<x-input>` SHALL render text inputs with labels, error messages, and help text, supporting validation state styling.

#### Scenario: Input renders with label and id
- **WHEN** rendering `<x-input name="email" label="Email" />`
- **THEN** it SHALL render a `<label>` with the text "Email"
- **AND** a `<input type="text">` with the matching `id` and `name` attributes

#### Scenario: Input shows error state with message
- **WHEN** rendering `<x-input name="email" :message="$errors->first('email')" />` with a validation error present
- **THEN** the input border SHALL be red
- **AND** an error message SHALL appear below the input

#### Scenario: Input supports help text
- **WHEN** rendering `<x-input name="email" help="Nous ne partagerons jamais votre email" />`
- **THEN** a help text paragraph SHALL appear below the input in muted styling

#### Scenario: Input passes type attribute
- **WHEN** rendering `<x-input name="password" type="password" />`
- **THEN** the rendered `<input>` SHALL have `type="password"`

### Requirement: Card component provides a contained layout surface

A reusable Blade component `<x-card>` SHALL render a contained section with padding, background, shadow, and optional header/footer slots.

#### Scenario: Default card has shadow and padding
- **WHEN** rendering `<x-card>Contenu</x-card>`
- **THEN** it SHALL render a `<div>` with white background, card shadow, rounded corners, and padding

#### Scenario: Card supports header and footer slots
- **WHEN** rendering a card with `<x-slot name="header">Titre</x-slot>` and `<x-slot name="footer">Actions</x-slot>`
- **THEN** the header SHALL appear at the top with a bottom border
- **AND** the footer SHALL appear at the bottom with a top border

#### Scenario: Card without header has no upper border
- **WHEN** rendering `<x-card>Contenu</x-card>` without a header slot
- **THEN** no separator line SHALL appear at the top

### Requirement: Table component renders structured data

A reusable Blade component `<x-table>` SHALL render an HTML table with optional header, striped rows, and responsive horizontal scroll.

#### Scenario: Table renders headers and rows
- **WHEN** passing a `:headers` array and a `:rows` collection
- **THEN** `<thead>` SHALL contain `<th>` elements from headers
- **AND** `<tbody>` SHALL contain one `<tr>` per row

#### Scenario: Table supports action column
- **WHEN** defining a header with `actions: true`
- **THEN** the last column SHALL be right-aligned and contain action buttons/link slots

#### Scenario: Empty table shows placeholder
- **WHEN** rendering the table with an empty `:rows` collection
- **THEN** it SHALL display a centered "Aucun résultat" message spanning all columns

### Requirement: Modal component displays overlay dialogs

A reusable Blade component `<x-modal>` SHALL render a centered dialog overlay using AlpineJS for show/hide behavior.

#### Scenario: Modal renders overlay and dialog
- **WHEN** rendering `<x-modal name="confirm">Contenu</x-modal>`
- **THEN** it SHALL render a backdrop overlay (semi-transparent black)
- **AND** a centered white dialog with the content

#### Scenario: Modal is hidden by default
- **WHEN** the page loads
- **THEN** the modal SHALL NOT be visible (`x-show="false"` initial state)

#### Scenario: Modal closes on backdrop click
- **WHEN** the user clicks the backdrop outside the dialog
- **THEN** the modal SHALL close

### Requirement: Badge component displays status labels

A reusable Blade component `<x-badge>` SHALL render small inline labels for status indicators, supporting color variants.

#### Scenario: Badge renders with text and variant color
- **WHEN** rendering `<x-badge variant="success">Validé</x-badge>`
- **THEN** it SHALL render a small inline span with green background and white text

#### Scenario: Badge variants map to semantic colors
- **WHEN** rendering each variant (`success`, `warning`, `danger`, `info`, `neutral`)
- **THEN** each variant SHALL render with the corresponding semantic color (green, yellow, red, blue, grey)

### Requirement: Alert component displays contextual messages

A reusable Blade component `<x-alert>` SHALL render dismissible alert banners for success, warning, error, and info messages.

#### Scenario: Alert renders with icon and message
- **WHEN** rendering `<x-alert type="success">Opération réussie</x-alert>`
- **THEN** it SHALL render a colored banner with a semantic icon and the message text

#### Scenario: Alert is dismissible
- **WHEN** rendering `<x-alert type="warning" dismissible>Attention</x-alert>`
- **THEN** a close button SHALL appear
- **AND** clicking it SHALL hide the alert via AlpineJS

### Requirement: Progress bar component displays completion

A reusable Blade component `<x-progress>` SHALL render a horizontal progress bar with optional label.

#### Scenario: Progress bar shows percentage
- **WHEN** rendering `<x-progress :value="75" />`
- **THEN** it SHALL render a full-width track with a filled bar at 75% width

#### Scenario: Progress bar supports color variants
- **WHEN** rendering `<x-progress :value="90" variant="success" />`
- **THEN** the filled bar SHALL use the success color (green)
- **WHEN** rendering `<x-progress :value="30" variant="danger" />`
- **THEN** the filled bar SHALL use the danger color (red)

#### Scenario: Progress bar shows label
- **WHEN** rendering `<x-progress :value="50" label="Analyse en cours" />`
- **THEN** the label text SHALL appear above the progress bar

### Requirement: All components meet accessibility requirements

Every design system component SHALL meet defined accessibility standards.

#### Scenario: All interactive elements are keyboard-focusable
- **WHEN** tabbing through the page
- **THEN** all buttons, inputs, links, and modal close buttons SHALL receive visible focus indicators

#### Scenario: Color contrast meets WCAG AA
- **WHEN** inspecting any text + background combination
- **THEN** the contrast ratio SHALL be at least 4.5:1 for normal text and 3:1 for large text

#### Scenario: Screen reader attributes are present
- **WHEN** rendering a modal
- **THEN** it SHALL have `role="dialog"` and `aria-modal="true"` and `aria-labelledby`
- **WHEN** rendering an alert
- **THEN** it SHALL have `role="alert"`

### Requirement: Design system is applied to all existing views

All existing Blade views SHALL use the new design system components instead of raw Breeze utility classes.

#### Scenario: Auth pages use design system components
- **WHEN** rendering login, register, password reset, and email verification views
- **THEN** they SHALL use `<x-card>`, `<x-input>`, and `<x-button>` components

#### Scenario: Offer pages use design system components
- **WHEN** rendering offer list, create, show, and submit-candidate views
- **THEN** they SHALL use `<x-card>`, `<x-table>`, `<x-badge>`, `<x-alert>`, `<x-input>`, and `<x-button>` as appropriate

#### Scenario: Profile pages use design system components
- **WHEN** rendering profile edit and delete views
- **THEN** they SHALL use `<x-card>`, `<x-input>`, `<x-alert>`, and `<x-button>` components

### Requirement: UX rules ensure consistent page structure

The design system SHALL define UX rules for form layouts, navigation, and data display that apply across all views.

#### Scenario: Forms follow consistent layout
- **WHEN** rendering any form
- **THEN** inputs SHALL be stacked vertically (one per row) with labels above
- **AND** submit/cancel buttons SHALL be right-aligned at the bottom

#### Scenario: Page titles follow consistent pattern
- **WHEN** rendering any page
- **THEN** the `<h1>` page title SHALL use the `h1` typography token
- **AND** SHALL be followed by a brief description text in muted styling
