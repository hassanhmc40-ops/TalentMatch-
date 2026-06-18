## Context

The Laravel AI SDK's `RemembersConversations` trait persists conversations to `agent_conversations` and `agent_conversation_messages` tables automatically. Currently, the `ConversationController` derives a conversation ID from the candidate analysis (`candidate-analysis-{id}`) but never stores a direct foreign key link. The application has no Eloquent models for these tables, no `CandidateAnalysis → conversations` relationship, and no indexes optimized for user-scoped conversation queries.

## Goals / Non-Goals

**Goals:**
- Add `candidate_analysis_id` nullable foreign key to `agent_conversations`
- Create `AgentConversation` and `AgentConversationMessage` Eloquent models
- Add `hasMany` from `CandidateAnalysis` and `belongsTo` from `AgentConversation`
- Add `user()` relationship on `AgentConversation` (user_id already exists on the table)
- Update `ConversationController::store()` to persist the `candidate_analysis_id`
- Add composite index on `(candidate_analysis_id, user_id, updated_at)`
- Existing SDK memory tables structure remains unchanged

**Non-Goals:**
- Changing the SDK's `RemembersConversations` trait or migration
- Modifying `agent_conversation_messages` schema (current indexes are sufficient)
- Building conversation list UI (future change)
- Changing the agent's instructions, tools, or system context logic

## Decisions

1. **Store `candidate_analysis_id` via direct DB update after agent call**
   The SDK does not expose a hook to attach metadata during conversation creation. After `$agent->send()`, use `AgentConversation::where('id', $conversationId)->update(['candidate_analysis_id' => ...])`. This is safe because the SDK creates the row on first `send()`.

2. **Models use table names from config**
   Read `config('ai.conversations.tables.conversations', 'agent_conversations')` and `config('ai.conversations.tables.messages', 'agent_conversation_messages')` for the table names to stay aligned with the SDK.

3. **`AgentConversation` uses `string` primary key**
   The SDK generates UUID primary keys. The model must set `$incrementing = false` and `$keyType = 'string'`.

4. **`AgentConversationMessage` has no direct `CandidateAnalysis` relationship**
   Messages belong to a conversation, which links to the analysis. Going through `message->conversation->candidateAnalysis` is preferred over adding a denormalized FK.

5. **New migration, do not modify the SDK's shipped migration**
   The SDK migration `2026_06_18_114934_create_agent_conversations_table` is already run. A new migration `xxxx_xx_xx_xxxxxx_add_candidate_analysis_id_to_agent_conversations` will add the column and index.

## Risks / Trade-offs

- **[Race condition]** If two requests create the same conversation simultaneously, the SDK handles this via its upsert logic; the `candidate_analysis_id` update is idempotent because it always targets the same conversation ID.
- **[Stale `candidate_analysis_id`]** If a conversation is created before the migration runs, existing rows will have `NULL`. The relationship uses `?->candidateAnalysis` which safely returns null; no data loss.
- **[SDK internal table name change]** If the SDK changes default table names, the models read from config, so they adapt automatically. The migration must reference the config key too.
- **[Rollback]** `down()` drops the column. Existing conversations lose the FK but are not deleted — data is preserved in the SDK tables.
