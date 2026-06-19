## ADDED Requirements

### Requirement: AgentConversation Eloquent model exists

The application SHALL provide an `AgentConversation` Eloquent model backed by the `agent_conversations` table (or the table name configured via `config('ai.conversations.tables.conversations')`).

#### Scenario: Model uses configurable table name
- **WHEN** inspecting `AgentConversation`
- **THEN** the model's table SHALL be `config('ai.conversations.tables.conversations', 'agent_conversations')`

#### Scenario: Model uses string primary key
- **WHEN** inspecting `AgentConversation`
- **THEN** `$incrementing` SHALL be `false`
- **AND** `$keyType` SHALL be `'string'`

### Requirement: AgentConversationMessage Eloquent model exists

The application SHALL provide an `AgentConversationMessage` Eloquent model backed by the `agent_conversation_messages` table (or the table name configured via `config('ai.conversations.tables.messages')`).

#### Scenario: Model uses configurable table name
- **WHEN** inspecting `AgentConversationMessage`
- **THEN** the model's table SHALL be `config('ai.conversations.tables.messages', 'agent_conversation_messages')`

#### Scenario: Model uses string primary key
- **WHEN** inspecting `AgentConversationMessage`
- **THEN** `$incrementing` SHALL be `false`
- **AND** `$keyType` SHALL be `'string'`

### Requirement: agent_conversations has candidate_analysis_id column

The `agent_conversations` table SHALL have a nullable `candidate_analysis_id` foreign key column referencing `candidate_analyses.id`.

#### Scenario: Column exists after migration
- **WHEN** inspecting the `agent_conversations` table schema
- **THEN** there SHALL be a `candidate_analysis_id` column of type `bigint` (unsigned) that is nullable
- **AND** it SHALL be indexed as part of a composite index with `user_id` and `updated_at`

#### Scenario: Existing rows are not affected
- **WHEN** the migration runs on a database with existing conversations
- **THEN** existing rows SHALL have `candidate_analysis_id` set to `null`

### Requirement: CandidateAnalysis has conversations relationship

`CandidateAnalysis` SHALL have a `conversations()` relationship returning `hasMany(AgentConversation::class, 'candidate_analysis_id')`.

#### Scenario: Relationship returns AgentConversation collection
- **WHEN** calling `$analysis->conversations`
- **THEN** it SHALL return a `Collection` of `AgentConversation` instances
- **AND** only conversations whose `candidate_analysis_id` matches the analysis shall be returned

### Requirement: AgentConversation has candidateAnalysis relationship

`AgentConversation` SHALL have a `candidateAnalysis()` relationship returning `belongsTo(CandidateAnalysis::class)`.

#### Scenario: Relationship returns the parent analysis
- **WHEN** calling `$conversation->candidateAnalysis`
- **THEN** it SHALL return the `CandidateAnalysis` instance if `candidate_analysis_id` is set
- **AND** SHALL return `null` if `candidate_analysis_id` is `null`

### Requirement: AgentConversation has messages and user relationships

`AgentConversation` SHALL have a `messages()` relationship and a `user()` relationship.

#### Scenario: Messages relationship
- **WHEN** calling `$conversation->messages`
- **THEN** it SHALL return a `Collection` of `AgentConversationMessage` instances ordered by `created_at`

#### Scenario: User relationship
- **WHEN** calling `$conversation->user`
- **THEN** it SHALL return the `User` instance who owns the conversation

### Requirement: ConversationController persists candidate_analysis_id

After a conversation is created via `ConversationalAgent::send()`, the `ConversationController::store()` method SHALL persist the `candidate_analysis_id` on the `agent_conversations` row.

#### Scenario: candidate_analysis_id is stored after agent reply
- **WHEN** the user sends a message to the conversational agent
- **THEN** the `agent_conversations` row with `id = 'candidate-analysis-{analysis->id}'` SHALL have `candidate_analysis_id` set to the analysis ID
