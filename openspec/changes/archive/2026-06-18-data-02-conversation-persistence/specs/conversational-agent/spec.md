## ADDED Requirements

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
