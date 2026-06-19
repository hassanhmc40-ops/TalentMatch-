## Purpose

Define the conversational agent architecture for TalentMatch, including the French-language HR assistant with tool calling, conversation memory, authorization-scoped tools, and chat UI.

## Requirements

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

#### Scenario: New conversation is created by the memory middleware
- **WHEN** an agent is prompted without a pre-set conversation ID
- **THEN** the `RememberConversation` middleware SHALL call `storeConversation()` with a UUID7
- **AND** SHALL create a row in `agent_conversations`

#### Scenario: Existing conversation is continued via continue()
- **WHEN** `$agent->continue($conversationId, $user)` is called before `prompt()`
- **THEN** the middleware SHALL use the provided ID for all message storage
- **AND** SHALL NOT create a new conversation

#### Scenario: Conversation messages are limited
- **WHEN** a conversation exceeds 100 messages
- **THEN** the agent SHALL only include the latest messages in context

### Requirement: Chat UI renders message history

The conversation view SHALL render a scrollable message list with user messages and agent responses, with proper styling and loading state.

#### Scenario: Messages are displayed in chronological order
- **WHEN** the user visits the conversation page
- **THEN** all previous messages SHALL be loaded from `agent_conversation_messages` and displayed in chronological order
- **AND** the most recent message SHALL be visible (auto-scroll)

#### Scenario: Loading state is shown during AI response
- **WHEN** the user submits a message via AJAX
- **THEN** a typing indicator with animated dots SHALL be displayed while the AI generates a response
- **AND** the input SHALL be disabled during loading
- **AND** the response SHALL stream token by token via SSE

### Requirement: Conversations are linked to candidate analyses

The `ConversationalAgent` conversation SHALL be linked to a `CandidateAnalysis` via the `candidate_analysis_id` column on the `agent_conversations` table.

#### Scenario: CandidateAnalysis link is persisted after first message
- **WHEN** a user sends the first message in a conversation for a given candidate analysis
- **THEN** the `agent_conversations` row SHALL have its `candidate_analysis_id` set to the analysis ID
- **AND** the link SHALL be persisted before the next request

#### Scenario: Follow-up messages reuse the same candidate analysis link
- **WHEN** a user sends follow-up messages in the same conversation
- **THEN** the `candidate_analysis_id` SHALL remain unchanged
- **AND** SHALL still reference the same `CandidateAnalysis`

### Requirement: Tool execution errors are returned to the model

If a tool's `handle()` method throws an exception, the SDK SHALL catch it and return the error to the model without failing the request.

#### Scenario: Tool exception is gracefully handled
- **WHEN** a tool throws an exception during execution
- **THEN** the error SHALL be returned to the AI model as a tool result
- **AND** the overall HTTP request SHALL succeed

#### Scenario: Empty tool result does not cause hallucination
- **WHEN** a tool returns data indicating no results found
- **THEN** the assistant SHALL inform the user truthfully
- **AND** SHALL NOT invent or guess data
