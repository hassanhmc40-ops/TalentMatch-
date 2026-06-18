## Purpose

HR agents need a dedicated form view to submit a candidate's name and CV text against a job offer. This capability covers the submission form UI, route, and the full request-to-redirect end-user flow.

## Requirements

### Requirement: Submission form is a dedicated page

The system SHALL provide a GET route at `/offres/{offre}/candidats/soumettre` that renders a Blade form view.

#### Scenario: Form page renders with offer context
- **WHEN** a user visits the submission form for an existing offer
- **THEN** the page SHALL display the offer title as a header
- **AND** SHALL render a form with fields "Nom du candidat" (text) and "Texte du CV" (textarea)
- **AND** SHALL display a "Retour à l'offre" link back to the offer detail page
- **AND** SHALL display a submit button labeled "Soumettre le candidat"

#### Scenario: Unauthorized user is rejected
- **WHEN** a user visits the submission form for an offer they do not own
- **THEN** the system SHALL return a 403 Forbidden response

### Requirement: Form submits via POST to existing route

The form SHALL POST to `offres.candidats.submit` (i.e., `POST /offres/{offre}/candidats`).

#### Scenario: Form action targets correct route
- **WHEN** the form is submitted
- **THEN** the form action SHALL be `POST /offres/{offre}/candidats`
- **AND** SHALL include a CSRF token

### Requirement: Successful submission redirects with French flash message

After a valid submission, the system SHALL redirect to the offer detail page with a French success flash message.

#### Scenario: Redirect after valid submission
- **WHEN** a user submits valid candidate data
- **THEN** the system SHALL redirect to `route('offres.show', $offre)`
- **AND** SHALL flash a success message: "Candidature soumise. L'analyse est en cours."

### Requirement: Validation errors display inline on the form

When validation fails, the system SHALL redirect back to the form page with validation errors displayed inline under each field.

#### Scenario: Validation errors shown on form
- **WHEN** a user submits invalid data (empty nom, empty cv_text, etc.)
- **THEN** the system SHALL redirect back to the form with validation errors
- **AND** SHALL display the errors inline under the respective fields using `<x-input-error>`
- **AND** SHALL preserve the previously submitted input values via `old()`

### Requirement: Duplicate submission redirects with error

When a duplicate candidate is detected, the system SHALL redirect back to the offer detail with the French duplicate error.

#### Scenario: Duplicate redirects to offer detail
- **WHEN** a user submits a candidate with the same name as an existing candidate for the same offer
- **THEN** the system SHALL redirect to `route('offres.show', $offre)` with a validation error
- **AND** the error message SHALL read: "Ce candidat a déjà été soumis pour cette offre."

### Requirement: Navigation link on offer detail page

The offer detail page SHALL provide a link to the submission form.

#### Scenario: "Soumettre un candidat" link visible
- **WHEN** a user views the offer detail page
- **THEN** the page SHALL display a link labeled "Soumettre un candidat" next to the "Candidats analysés" section header
- **AND** the link SHALL point to the submission form URL
