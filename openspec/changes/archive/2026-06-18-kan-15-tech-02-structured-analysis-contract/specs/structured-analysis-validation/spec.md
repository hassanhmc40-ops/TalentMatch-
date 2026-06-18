## ADDED Requirements

### Requirement: All string fields must be validated

Every string-typed field (`niveau_etudes`, `justification`) SHALL be validated as a non-empty string with a maximum length.

#### Scenario: Valid string values pass
- **WHEN** the AI returns `"niveau_etudes": "Bac+5"` and `"justification": "Profil correspond bien"` with each field ≤ 5000 characters
- **THEN** string validation SHALL pass

#### Scenario: Empty string is rejected
- **WHEN** the AI returns `"justification": ""` or a field that is not a string
- **THEN** the analysis SHALL be rejected with error "La justification est obligatoire."

#### Scenario: String exceeds maximum length
- **WHEN** the AI returns a string longer than 5000 characters
- **THEN** the analysis SHALL be rejected

### Requirement: Array fields must be validated as non-empty arrays of strings

Every array field (`competences_extraites`, `langues`, `points_forts`, `lacunes`, `competences_manquantes`) SHALL be validated as an array containing only string values.

#### Scenario: Valid string array passes
- **WHEN** the AI returns `"competences_extraites": ["PHP", "Laravel", "MySQL"]`
- **THEN** array validation SHALL pass

#### Scenario: Empty array is allowed for optional arrays
- **WHEN** the AI returns `"langues": []` or `"competences_manquantes": []` (valid JSON, zero items)
- **THEN** validation SHALL pass with an empty array

#### Scenario: Non-array value is rejected
- **WHEN** the AI returns `"competences_extraites": "PHP, Laravel"` (string instead of array)
- **THEN** the analysis SHALL be rejected

#### Scenario: Array containing non-string values is rejected
- **WHEN** the AI returns `"competences_extraites": ["PHP", 123, true]`
- **THEN** the analysis SHALL be rejected

### Requirement: years_experience must be a non-negative integer

The `annees_experience` field SHALL be validated as an integer ≥ 0 and ≤ 50.

#### Scenario: Valid years_experience passes
- **WHEN** the AI returns `"annees_experience": 5`
- **THEN** validation SHALL pass

#### Scenario: Negative years_experience is rejected
- **WHEN** the AI returns `"annees_experience": -1`
- **THEN** the analysis SHALL be rejected

#### Scenario: years_experience exceeding maximum is rejected
- **WHEN** the AI returns `"annees_experience": 99`
- **THEN** the analysis SHALL be rejected

### Requirement: matching_score must be an integer between 0 and 100

The `matching_score` field SHALL be validated as an integer whose value is ≥ 0 and ≤ 100.

#### Scenario: Valid matching_score passes
- **WHEN** the AI returns `"matching_score": 78`
- **THEN** validation SHALL pass

#### Scenario: matching_score below 0 is rejected
- **WHEN** the AI returns `"matching_score": -5`
- **THEN** the analysis SHALL be rejected

#### Scenario: matching_score above 100 is rejected
- **WHEN** the AI returns `"matching_score": 150`
- **THEN** the analysis SHALL be rejected

#### Scenario: Non-integer matching_score is rejected
- **WHEN** the AI returns `"matching_score": 78.5`
- **THEN** the analysis SHALL be rejected

### Requirement: recommandation must be a valid enum value

The `recommandation` field SHALL be validated as one of: `convoquer`, `attente`, `rejeter`.

#### Scenario: Valid recommendation passes
- **WHEN** the AI returns `"recommandation": "convoquer"`
- **THEN** validation SHALL pass

#### Scenario: Invalid recommendation string is rejected
- **WHEN** the AI returns `"recommandation": "embaucher"` or any value not in the enum
- **THEN** the analysis SHALL be rejected

### Requirement: Missing required fields are rejected

If any required field is missing from the AI response JSON, the analysis SHALL be rejected.

#### Scenario: Missing field causes rejection
- **WHEN** the AI response is missing `"matching_score"` or `"recommandation"` or any other required key
- **THEN** the analysis SHALL be rejected with a French error listing the missing field

### Requirement: Validation failures are logged

Every validation failure SHALL be logged at `error` level with the AI response preview and the failing field details.

#### Scenario: Validation error is logged
- **WHEN** a validation rule fails
- **THEN** a log entry SHALL be written with level `error`, including the failing field and a truncated preview of the AI response
- **AND** the raw AI response SHALL NOT be exposed in the UI
