## 1. Migration & Schema

- [x] 1.1 Generate migration to add `candidate_analysis_id` to `agent_conversations` table
- [x] 1.2 Add `foreignId('candidate_analysis_id')->nullable()->constrained('candidate_analyses')->cascadeOnDelete()` column
- [x] 1.3 Add composite index on `(candidate_analysis_id, user_id, updated_at)`
- [x] 1.4 Write `down()` method that drops the column and index
- [x] 1.5 Run migration and verify schema via `php artisan db:show` or Boost schema tool

## 2. Eloquent Models

- [x] 2.1 Create `AgentConversation` model with `$incrementing = false`, `$keyType = 'string'`, and table name from config
- [x] 2.2 Add `$fillable` with `['candidate_analysis_id']` to `AgentConversation`
- [x] 2.3 Add `candidateAnalysis()` belongsTo relationship on `AgentConversation`
- [x] 2.4 Add `user()` belongsTo relationship on `AgentConversation`
- [x] 2.5 Add `messages()` hasMany relationship on `AgentConversation` ordered by `created_at`
- [x] 2.6 Create `AgentConversationMessage` model with `$incrementing = false`, `$keyType = 'string'`, and table name from config
- [x] 2.7 Add `conversation()` belongsTo relationship on `AgentConversationMessage`
- [x] 2.8 Add `conversations()` hasMany relationship on `CandidateAnalysis` model
- [x] 2.9 Add scope `byUser($userId)` on `AgentConversation` for user-scoped queries

## 3. Controller Update

- [x] 3.1 Update `ConversationController::store()` to update `candidate_analysis_id` on the `agent_conversations` row after `$agent->send()`
- [x] 3.2 Use `AgentConversation::where('id', $conversationId)->update(['candidate_analysis_id' => $analysis->id])` (idempotent)

## 4. Testing

- [x] 4.1 Write migration test: assert column exists, index exists, nullable
- [x] 4.2 Write model test: `AgentConversation` uses correct table, string PK, relationships
- [x] 4.3 Write model test: `AgentConversationMessage` uses correct table, string PK, relationships
- [x] 4.4 Write model test: `CandidateAnalysis::conversations()` returns related conversations
- [x] 4.5 Write controller test: `candidate_analysis_id` is stored after conversation creation
- [x] 4.6 Write controller test: follow-up messages preserve the same `candidate_analysis_id`
- [x] 4.7 Write scope test: `byUser()` returns only conversations for the given user

## 5. Code Quality & Verification

- [x] 5.1 Run `vendor/bin/pint` to format all changed PHP files
- [x] 5.2 Run full test suite: `php artisan test --compact`
- [x] 5.3 Verify no N+1 queries in conversation-related code
- [x] 5.4 Run queue worker and verify conversation persistence end-to-end via browser or Tinker
