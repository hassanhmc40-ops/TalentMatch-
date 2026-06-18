## Purpose

HR agents need to create job offers as the foundation for candidate screening. This capability covers the creation flow: displaying a French-language form, validating input, persisting the offer with proper authorization, and providing success feedback. Every job offer belongs to the authenticated user.

## Requirements

### Requirement: HR user can access the job offer creation form

The system SHALL provide an authenticated HR user with a page containing a form to create a new job offer.

#### Scenario: Authenticated user opens creation form
- **WHEN** an authenticated HR user navigates to `/offres/creer`
- **THEN** the system SHALL display a form with fields: title (titre), description (description), required skills (compétences requises), minimum experience (années d'expérience minimum)

#### Scenario: Unauthenticated user is redirected to login
- **WHEN** an unauthenticated user navigates to `/offres/creer`
- **THEN** the system SHALL redirect to the login page

#### Scenario: Unverified email user is prompted to verify
- **WHEN** an authenticated but unverified user navigates to `/offres/creer`
- **THEN** the system SHALL redirect to the email verification notice page

### Requirement: HR user can submit a valid job offer

The system SHALL persist a job offer when the HR user submits valid data via the creation form.

#### Scenario: Successful creation with all fields
- **WHEN** an authenticated HR user submits valid data: title "Développeur PHP", description "Nous recherchons un développeur PHP expérimenté...", required_skills ["PHP", "Laravel", "MySQL"], min_experience_years 3
- **THEN** the system SHALL create a new job offer record in the database
- **AND** the job offer SHALL be associated with the authenticated user
- **AND** the system SHALL redirect with a success flash message

#### Scenario: Created offer belongs to the authenticated user
- **WHEN** an authenticated HR user creates a job offer
- **THEN** the `user_id` on the job offer SHALL match the authenticated user's ID
- **AND** the `user_id` SHALL NOT be read from request input

### Requirement: Form validation rejects invalid input

The system SHALL validate all input fields before persisting a job offer and display French error messages for invalid data.

#### Scenario: Missing title
- **WHEN** an HR user submits the form without a title
- **THEN** the system SHALL display a validation error: "Le titre est obligatoire."

#### Scenario: Description too short
- **WHEN** an HR user submits a description with fewer than 10 characters
- **THEN** the system SHALL display a validation error indicating the minimum length

#### Scenario: No required skills
- **WHEN** an HR user submits the form with an empty required skills list
- **THEN** the system SHALL display a validation error: "Au moins une compétence est requise."

#### Scenario: Duplicate skill entries
- **WHEN** an HR user submits the same skill twice in the required skills list
- **THEN** the system SHALL display a validation error indicating duplicate values are not allowed

#### Scenario: Negative minimum experience
- **WHEN** an HR user submits a negative value for minimum experience years
- **THEN** the system SHALL display a validation error indicating the value must be 0 or more

#### Scenario: Excessive minimum experience
- **WHEN** an HR user submits a value greater than 50 for minimum experience years
- **THEN** the system SHALL display a validation error indicating the maximum value is 50

#### Scenario: Title exceeds maximum length
- **WHEN** an HR user submits a title longer than 255 characters
- **THEN** the system SHALL display a validation error indicating the maximum length

### Requirement: Job offer data is persisted correctly

The system SHALL store all job offer data with correct types and associations.

#### Scenario: Required skills stored as JSON array
- **WHEN** an HR user submits required_skills ["PHP", "Laravel"]
- **THEN** the `required_skills` column SHALL contain a valid JSON array `["PHP", "Laravel"]`
- **AND** accessing `$jobOffer->required_skills` SHALL return a PHP array

#### Scenario: Minimum experience stored as integer
- **WHEN** an HR user submits min_experience_years 3
- **THEN** the `min_experience_years` column SHALL store the integer value 3

#### Scenario: Job offer without required skills is rejected
- **WHEN** an HR user submits required_skills as an empty array
- **THEN** the system SHALL display a validation error
- **AND** no job offer SHALL be created

### Requirement: UI labels and messages are in French

The system SHALL display all form labels, placeholders, error messages, and success messages in French.

#### Scenario: Form labels are in French
- **WHEN** the creation form is displayed
- **THEN** field labels SHALL be: "Titre de l'offre", "Description", "Compétences requises", "Années d'expérience minimum"

#### Scenario: Success message is in French
- **WHEN** a job offer is successfully created
- **THEN** the flash message SHALL read: "L'offre d'emploi a été créée avec succès."
