## Purpose

Define the formal contracts, authorization rules, error handling, and acceptance criteria for the three candidate analysis tools used by the conversational agent.

## Requirements

### Requirement: Tool implements the Tool contract

Each candidate analysis tool SHALL implement `Laravel\Ai\Contracts\Tool`, live in `app/Ai/Tools/`, and define `description()`, `handle(Request $request): string`, and `schema(JsonSchema $schema): array`.

#### Scenario: Tool contract is fulfilled
- **WHEN** inspecting a tool class
- **THEN** it SHALL implement `Laravel\Ai\Contracts\Tool`
- **AND** SHALL have a `description()` returning a French-language string
- **AND** SHALL have a `handle(Request)` method returning a string
- **AND** SHALL have a `schema(JsonSchema)` method returning an array

### Requirement: getCandidateAnalysis retrieves full candidate analysis

The `GetCandidateAnalysis` tool SHALL accept a `candidat_id` parameter and return the complete analysis data as a JSON string, scoped to the authenticated user's job offers.

#### Scenario: Returns analysis for authorized user
- **WHEN** the tool is called with a valid `candidat_id` whose analysis belongs to the authenticated user's offer
- **THEN** it SHALL return a JSON string containing candidate name, offer title, extracted skills, years of experience, education level, languages, matching score, strengths, gaps, missing skills, recommendation, and justification

#### Scenario: Returns error for unauthorized or missing analysis
- **WHEN** the tool is called with a `candidat_id` that has no analysis
- **OR** the analysis belongs to another user's offer
- **THEN** it SHALL return "Analyse non trouvée ou accès non autorisé."

### Requirement: getJobRequirements retrieves job offer criteria

The `GetJobRequirements` tool SHALL accept an `offre_id` parameter and return the job offer's title, description, required skills, and minimum experience as a JSON string, scoped to the authenticated user.

#### Scenario: Returns offer for authorized user
- **WHEN** the tool is called with a valid `offre_id` owned by the authenticated user
- **THEN** it SHALL return a JSON string containing the offer's title, description, required skills, and minimum experience years

#### Scenario: Returns error for unauthorized or missing offer
- **WHEN** the tool is called with an `offre_id` that does not exist
- **OR** the offer belongs to another user
- **THEN** it SHALL return "Offre non trouvée ou accès non autorisé."

### Requirement: compareCandidates compares two analyses for the same offer

The `CompareCandidates` tool SHALL accept `analyse_id_1` and `analyse_id_2` parameters and return a JSON comparison string. Both analyses MUST belong to the same job offer and be accessible to the authenticated user.

#### Scenario: Returns comparison for authorized same-offer analyses
- **WHEN** the tool is called with two valid analysis IDs belonging to the same offer accessible by the user
- **THEN** it SHALL return a JSON string containing each candidate's name, score, strengths, gaps, recommendation, and the score difference

#### Scenario: Returns error for cross-offer comparison
- **WHEN** the two analyses belong to different job offers
- **THEN** it SHALL return "Erreur : Impossible de comparer des candidats de différentes offres."

#### Scenario: Returns error when either analysis is unauthorized or missing
- **WHEN** either analysis ID does not exist or belongs to another user's offer
- **THEN** it SHALL return "Analyse non trouvée ou accès non autorisé."

### Requirement: Tools perform authorization inside handle()

Each tool SHALL perform authorization via `auth()->id()` scoped queries inside its `handle()` method. Authorization failures SHALL return a string error message, not throw an exception.

#### Scenario: Silent error for unauthorized access
- **WHEN** authorization fails inside `handle()`
- **THEN** the tool SHALL return a French error string
- **AND** SHALL NOT throw an exception

### Requirement: Tool exceptions are caught by the SDK

If a tool's `handle()` method throws an unexpected exception, the SDK SHALL catch it and return the error to the AI model without failing the HTTP request.

#### Scenario: Exception is gracefully handled
- **WHEN** a tool throws an exception during execution
- **THEN** the SDK SHALL catch the exception
- **AND** the error message SHALL be returned to the AI model as a tool result
- **AND** the overall request SHALL succeed
