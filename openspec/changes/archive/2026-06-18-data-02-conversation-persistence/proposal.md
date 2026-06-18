## Why

The Laravel AI SDK provides raw conversation tables (`agent_conversations`, `agent_conversation_messages`) and automatic persistence via `RemembersConversations`, but the application has no Eloquent models, no typed relationships from `CandidateAnalysis` to its conversations, no migration to link analyses directly to conversations, and no indexes for common HR query patterns (listing all conversations for an offer, finding conversations by user). This makes it impossible to build UI features like a conversation history page, an offer-level conversation log, or an admin conversation listing without raw database queries.

## What Changes

- Add `candidate_analysis_id` nullable foreign key to `agent_conversations` table
- Create `AgentConversation` model (backing the SDK's `agent_conversations` table)
- Create `AgentConversationMessage` model (backing the SDK's `agent_conversation_messages` table)
- Add `hasMany` relationship from `CandidateAnalysis` → `AgentConversation`
- Add `belongsTo` relationship from `AgentConversation` → `CandidateAnalysis`
- Add scope to `AgentConversation` for filtering by user
- Add composite index for `(candidate_analysis_id, user_id, updated_at)` on conversations
- Update `ConversationController` to store the `candidate_analysis_id` when creating conversations
- Verify existing `conversation_index` on messages table covers common queries
- Add `candidate_analysis_id` to any existing conversation factory/seed if present

## Capabilities

### New Capabilities
- `conversation-persistence`: Eloquent models, relationships, migration, and indexes for the SDK-managed conversation tables. Bridges the gap between raw `agent_conversations` and the application domain model.

### Modified Capabilities
- `conversational-agent`: Add requirement that conversations link to a `CandidateAnalysis` via `candidate_analysis_id`. No existing requirements change—only the persistence mechanism is enriched.

## Impact

- **database**: New migration adding `candidate_analysis_id` to `agent_conversations`. No schema change to `agent_conversation_messages` (existing `conversation_index` already covers `(conversation_id, user_id, updated_at)`).
- **models**: Two new Eloquent models (`AgentConversation`, `AgentConversationMessage`) using the SDK's table names from config.
- **relationships**: `CandidateAnalysis::conversations()`, `AgentConversation::candidateAnalysis()`, `AgentConversation::messages()`, `AgentConversation::user()`, `AgentConversationMessage::conversation()`.
- **controller**: `ConversationController::store()` updates to set the `candidate_analysis_id` on conversation creation (via `agent->conversation()` metadata or direct DB update).
- **tests**: Model relationship tests, migration tests, scoped query tests.
- No changes to the agent's instructions, tools, or conversation memory logic.
