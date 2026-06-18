## Purpose

This delta adds requirements for the authorization gating and end-to-end submission flow to the existing `candidate-submission` spec. The underlying validation logic is unchanged.

## Changes

### Add: Authorization check before submission

Add the following requirement and scenarios to the `candidate-submission` spec:

#### Requirement: Submission is gated by offer ownership

The system SHALL reject candidate submissions when the authenticated user does not own the target job offer.

##### Scenario: Own offer passes authorization
- **WHEN** an authenticated user submits a candidate for their own job offer
- **THEN** the system SHALL proceed with validation

##### Scenario: Another user's offer returns 403
- **WHEN** an authenticated user submits a candidate for a job offer belonging to another user
- **THEN** the system SHALL return a 403 Forbidden response
- **AND** SHALL NOT create a Candidate or CandidateAnalysis record
- **AND** SHALL NOT dispatch any queue job

### Add: Successful submission shows flash message

Insert into the "Analysis is dispatched via queue after submission validation" requirement:

##### Scenario: Flash message on successful submission
- **WHEN** an HR user submits valid candidate data
- **THEN** the system SHALL redirect to the offer detail page
- **AND** SHALL flash a success message: "Candidature soumise. L'analyse est en cours."

### Modify: Duplicate submission scenario

Replace the error-redirect scenario in "Duplicate candidate submission" with:

#### Scenario: Duplicate name for same offer redirects with validation error
- **WHEN** an HR user submits a candidate named "Jean Dupont" for offer ID 1
- **AND** a candidate with the same name already exists for offer ID 1
- **THEN** the system SHALL redirect to `route('offres.show', $offre)` with a validation error
- **AND** the error message SHALL read: "Ce candidat a déjà été soumis pour cette offre."
- **AND** SHALL NOT create a duplicate CandidateAnalysis record
