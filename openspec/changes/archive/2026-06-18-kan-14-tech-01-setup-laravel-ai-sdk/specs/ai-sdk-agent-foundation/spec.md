## ADDED Requirements

### Requirement: App directory structure for AI agents and tools

The system SHALL create an `app/Ai/` directory with `Agents/` and `Tools/` subdirectories.

#### Scenario: Directory structure is created
- **WHEN** the project is inspected
- **THEN** `app/Ai/Agents/` SHALL exist
- **AND** `app/Ai/Tools/` SHALL exist

### Requirement: CvAnalysisAgent supports structured CV analysis

The system SHALL provide a `CvAnalysisAgent` that implements `HasStructuredOutput` with the CV analysis JSON schema contract.

#### Scenario: Agent implements structured output contract
- **WHEN** `CvAnalysisAgent::make()` is instantiated
- **THEN** it SHALL implement `Laravel\Ai\Contracts\Agent`
- **AND** it SHALL implement `Laravel\Ai\Contracts\HasStructuredOutput`

#### Scenario: Schema matches the CV analysis contract
- **WHEN** the agent's `schema` method is called
- **THEN** it SHALL return the full contract: `competences_extraites` (array of strings), `annees_experience` (integer), `niveau_etudes` (string), `langues` (array of strings), `matching_score` (integer, min 0, max 100), `points_forts` (array of strings), `lacunes` (array of strings), `competences_manquantes` (array of strings), `recommandation` (enum: convoquer, attente, rejeter), `justification` (string)

#### Scenario: Agent prompt returns structured response
- **WHEN** the agent is prompted with a CV text and job offer requirements
- **THEN** the response SHALL be a `StructuredAgentResponse`
- **AND** the response SHALL be accessible as an array matching the schema keys

### Requirement: ConversationalAgent supports multi-turn conversation with memory

The system SHALL provide a `ConversationalAgent` that implements `Conversational` using the `RemembersConversations` trait.

#### Scenario: Agent implements conversational interface
- **WHEN** `ConversationalAgent::make()` is instantiated
- **THEN** it SHALL implement `Laravel\Ai\Contracts\Agent`
- **AND** it SHALL implement `Laravel\Ai\Contracts\Conversational`

#### Scenario: Conversation history is persisted
- **WHEN** the agent uses the `RemembersConversations` trait
- **THEN** conversation messages SHALL be automatically stored in the `agent_conversation_messages` table
- **AND** previous messages SHALL be loaded when continuing a conversation

#### Scenario: Agent can be scoped to a user
- **WHEN** `->forUser($user)` is called on the agent
- **THEN** the conversation SHALL be associated with that user
- **AND** the `conversationId` SHALL be returned in the response

### Requirement: ConversationalAgent supports tools for database retrieval

The system SHALL provide tool classes that the conversational agent can use to retrieve real data from the database.

#### Scenario: GetCandidateAnalysis tool retrieves analysis
- **WHEN** the conversational agent uses the `GetCandidateAnalysis` tool
- **THEN** it SHALL return the full candidate analysis from the database by candidate ID

#### Scenario: GetJobRequirements tool retrieves offer criteria
- **WHEN** the conversational agent uses the `GetJobRequirements` tool
- **THEN** it SHALL return the job offer criteria (title, skills, experience) by offer ID

#### Scenario: CompareCandidates tool compares two analyses
- **WHEN** the conversational agent uses the `CompareCandidates` tool
- **THEN** it SHALL compare two candidate analyses for the same job offer
- **AND** it SHALL return the differences in scores, strengths, gaps, and recommendations

### Requirement: User model supports conversations

The system SHALL add the `HasConversations` trait to the `User` model to enable conversation association.

#### Scenario: User has conversations relationship
- **WHEN** the `User` model is inspected
- **THEN** it SHALL use the `Laravel\Ai\Concerns\HasConversations` trait
- **AND** the `conversations()` relationship SHALL be available

#### Scenario: Conversations are scoped to user
- **WHEN** querying `$user->conversations()`
- **THEN** it SHALL return only conversations belonging to that user
- **AND** conversations SHALL be ordered by `updated_at` descending
