## ADDED Requirements

### Requirement: Agent uses French system instructions with tool-usage rules

The `ConversationalAgent::instructions()` SHALL return a French-language system prompt that defines the assistant role, tool-usage rules, and anti-hallucination constraints.

#### Scenario: Instructions are in French
- **WHEN** inspecting `ConversationalAgent::instructions()`
- **THEN** the instructions SHALL be in French
- **AND** SHALL describe the assistant as an HR assistant specialized in candidate analysis

#### Scenario: Instructions mandate tool usage
- **WHEN** inspecting `ConversationalAgent::instructions()`
- **THEN** the instructions SHALL state that the assistant MUST use tools to retrieve data
- **AND** SHALL forbid inventing scores, missing skills, job requirements, or comparison results

### Requirement: Agent exposes three mandatory tools

The `ConversationalAgent` SHALL register `GetCandidateAnalysis`, `GetJobRequirements`, and `CompareCandidates` as available tools via the `tools()` method.

#### Scenario: All three tools are registered
- **WHEN** inspecting `ConversationalAgent::tools()`
- **THEN** it SHALL return exactly three tools
- **AND** the tools SHALL include `GetCandidateAnalysis`, `GetJobRequirements`, and `CompareCandidates`

### Requirement: Tools scope data to authenticated user's offers

Each tool SHALL only return data for job offers owned by the authenticated user. If the user does not own the offer, the tool SHALL return a French error message.

#### Scenario: GetCandidateAnalysis scopes to user's offers
- **WHEN** the tool is called with a `candidat_id` whose analysis belongs to another user's offer
- **THEN** it SHALL return "Analyse non trouvée ou accès non autorisé."

#### Scenario: GetJobRequirements scopes to user's offers
- **WHEN** the tool is called with an `offre_id` that belongs to another user
- **THEN** it SHALL return "Offre non trouvée ou accès non autorisé."

#### Scenario: CompareCandidates rejects cross-offer comparison
- **WHEN** called with `analyse_id_1` and `analyse_id_2` that belong to different job offers
- **THEN** it SHALL return "Erreur : Impossible de comparer des candidats de différentes offres."
- **WHEN** either analysis belongs to an offer the user does not own
- **THEN** it SHALL return "Analyse non trouvée ou accès non autorisé."

### Requirement: Agent uses conversation memory

The `ConversationalAgent` SHALL use the `RemembersConversations` trait to persist conversation history across turns.

#### Scenario: Follow-up question reuses context
- **WHEN** a user sends a follow-up message in the same conversation
- **THEN** the agent SHALL have access to the previous messages from the same conversation
- **AND** SHALL NOT require repeating the candidate or offer context

### Requirement: Chat UI renders message history

The conversation view SHALL render a scrollable message list with user messages and agent responses, with proper styling and loading state.

#### Scenario: Messages are displayed in chronological order
- **WHEN** the user visits the conversation page
- **THEN** all previous messages SHALL be displayed in chronological order
- **AND** the most recent message SHALL be visible (auto-scroll)

#### Scenario: Loading state is shown during AI response
- **WHEN** the user submits a message
- **THEN** a loading indicator SHALL be displayed while the AI generates a response
- **AND** the input SHALL be disabled during loading
