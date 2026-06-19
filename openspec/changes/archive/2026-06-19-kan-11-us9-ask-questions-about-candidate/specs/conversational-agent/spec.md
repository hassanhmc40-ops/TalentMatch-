## MODIFIED Requirements

### Requirement: Agent uses French system instructions with tool-usage rules

The `ConversationalAgent` SHALL accept dynamic context (candidate name, offer title, analysis IDs, matching score, recommendation, key skills) via a `setContext()` method, and SHALL include this context in the system instructions when set.

#### Scenario: Context is prepended to instructions when set
- **WHEN** `setContext()` is called with candidate name, offer title, analysis data, and IDs
- **THEN** `instructions()` SHALL return a string that includes a "Contexte actuel" block with the provided data
- **AND** the context block SHALL be positioned before the role description

#### Scenario: Instructions work without context
- **WHEN** `setContext()` has not been called
- **THEN** `instructions()` SHALL return the same base instructions as before
- **AND** no context block SHALL be prepended

#### Scenario: Answer formatting rules are defined
- **WHEN** inspecting `instructions()`
- **THEN** the instructions SHALL include answer formatting rules per question type (score, skills, recommendation, comparison)
- **AND** SHALL include a follow-up question handling rule
- **AND** SHALL include an out-of-scope handling rule

### Requirement: Conversation controller injects context before prompting

The `ConversationController` SHALL call `setContext()` on the agent with the current analysis data before calling `prompt()` or `stream()`.

#### Scenario: Store method injects context
- **WHEN** the controller receives a POST message
- **THEN** the controller SHALL extract analysis data (candidate name, offer title, IDs, score, recommendation, key skills) and pass it to the agent via `setContext()`
- **AND** SHALL call `prompt()` after context is set

#### Scenario: Stream method injects context
- **WHEN** the controller receives a streaming request
- **THEN** the controller SHALL extract the same analysis data and pass it to the agent via `setContext()`
- **AND** SHALL call `stream()` after context is set

### Requirement: Agent handles follow-up questions using context

The agent SHALL use conversation memory and injected context to answer follow-up questions without requiring the user to re-specify the candidate or offer.

#### Scenario: Follow-up question does not need candidate name
- **WHEN** the user asks "Quel est son score ?" after a previous question about the candidate
- **THEN** the agent SHALL understand that "son" refers to the candidate in the current context
- **AND** SHALL answer using the tool with the `candidat_id` from the injected context

#### Scenario: Follow-up in same conversation reuses analysis ID
- **WHEN** the user sends a follow-up message
- **THEN** the agent SHALL have access to the previously established `candidat_id`, `offre_id`, and `analyse_id` from the injected context
- **AND** SHALL NOT ask the user for these IDs again

### Requirement: Agent answers common question types with structured formatting

The agent SHALL format answers differently depending on the question type: score, skills, recommendation, or comparison.

#### Scenario: Score question includes exact value and level
- **WHEN** the user asks about the matching score
- **THEN** the agent SHALL respond with the exact score number and the level label (Faible / Moyen / Bon / Excellent)

#### Scenario: Skills question lists skills with bullet points
- **WHEN** the user asks about extracted skills
- **THEN** the agent SHALL list the skills using bullet points
- **AND** SHALL also mention missing skills when relevant

#### Scenario: Recommendation question cites justification
- **WHEN** the user asks about the recommendation
- **THEN** the agent SHALL state the recommendation label and cite the justification from the analysis

#### Scenario: Comparison question presents structured comparison
- **WHEN** the user asks to compare two candidates
- **THEN** the agent SHALL present scores, strengths, gaps, and recommendations for each candidate
- **AND** SHALL highlight the score difference

### Requirement: Agent handles out-of-scope questions politely

The agent SHALL politely decline questions that are unrelated to candidate analysis, job offers, or HR tasks.

#### Scenario: Out-of-scope question is declined
- **WHEN** the user asks a general knowledge question (e.g., weather, news, sports)
- **THEN** the agent SHALL respond that it is specialized in candidate analysis
- **AND** SHALL redirect the user to ask about the current candidate
