## ADDED Requirements

### Requirement: StoreJobOfferRequest authorizes using the JobOfferPolicy

The system SHALL authorize job offer creation requests using `JobOfferPolicy::create` before running validation.

#### Scenario: Authenticated user passes authorization
- **WHEN** an authenticated HR user submits the job offer creation form
- **THEN** the `authorize()` method SHALL call `JobOfferPolicy::create`
- **AND** the request SHALL proceed to validation if the policy returns `true`

#### Scenario: Unauthenticated user is rejected
- **WHEN** an unauthenticated user submits the job offer creation form
- **THEN** the `authorize()` method SHALL return `false`
- **AND** the system SHALL return a 403 Forbidden response

### Requirement: StoreJobOfferRequest sanitizes input before validation

The system SHALL trim whitespace from all text fields and filter empty entries from the required skills array before running validation rules.

#### Scenario: Whitespace-only title becomes empty and fails validation
- **WHEN** an HR user submits a title containing only spaces "   "
- **THEN** the `prepareForValidation()` method SHALL trim it to an empty string
- **AND** the required rule SHALL reject it with "Le titre est obligatoire."

#### Scenario: Skills array with empty strings is filtered
- **WHEN** an HR user submits required_skills containing ["PHP", "", "MySQL"]
- **THEN** the `prepareForValidation()` method SHALL remove the empty string entry
- **AND** validation SHALL see `required_skills` as ["PHP", "MySQL"]

### Requirement: StoreJobOfferRequest uses French attribute names

The system SHALL use French field names for validation error placeholders via the `attributes()` method.

#### Scenario: Error message uses French attribute name for min_experience_years
- **WHEN** the min_experience_years validation fails
- **THEN** the error message SHALL contain "années d'expérience" instead of "min experience years"
