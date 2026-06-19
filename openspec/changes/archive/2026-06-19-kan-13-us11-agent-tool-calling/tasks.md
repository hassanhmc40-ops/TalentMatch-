## 1. Tool Naming

- [x] 1.1 Add `name()` method to `GetCandidateAnalysis` returning `get_candidate_analysis`
- [x] 1.2 Add `name()` method to `GetJobRequirements` returning `get_job_requirements`
- [x] 1.3 Add `name()` method to `CompareCandidates` returning `compare_candidates`

## 2. Exception Handling

- [x] 2.1 Wrap `GetCandidateAnalysis::handle()` body in try/catch, return French error string on exception
- [x] 2.2 Wrap `GetJobRequirements::handle()` body in try/catch, return French error string on exception
- [x] 2.3 Wrap `CompareCandidates::handle()` body in try/catch, return French error string on exception

## 3. Middleware & Audit Trail

- [ ] 3.1 Create migration for `tool_executions` table (id, conversation_message_id, tool_name, arguments JSON, result_summary, duration_ms, success, error_message, created_at)
- [ ] 3.2 Create `ToolExecution` model with fillable fields and relationships
- [ ] 3.3 Create `LogToolCalls` middleware class that listens to `InvokingTool` / `ToolInvoked` events and persists to `tool_executions`
- [ ] 3.4 Implement `HasMiddleware` on `ConversationalAgent`, return `LogToolCalls` middleware in `middleware()` method

## 4. Streaming UI

- [ ] 4.1 Update `ConversationController::stream()` to emit SSE events for tool invocations (tool name + status)
- [ ] 4.2 Update `resources/views/conversations/show.blade.php` to display tool call badges during streaming

## 5. Test Coverage

- [ ] 5.1 Write test: each tool returns correct snake_case name from `name()`
- [ ] 5.2 Write test: tool exception returns French error message instead of throwing
- [ ] 5.3 Write test: `ConversationalAgent` implements `HasMiddleware` and returns middleware array
- [ ] 5.4 Write test: `ToolExecution` model persists and retrieves correctly
- [ ] 5.5 Write test: streaming SSE emits tool invocation events

## 6. Code Quality & Archive

- [ ] 6.1 Run `vendor/bin/pint` for code formatting
- [ ] 6.2 Run full test suite: `php artisan test --compact`
- [ ] 6.3 Sync delta spec to main spec (conversational-agent)
- [ ] 6.4 Sync new spec to main specs (agent-tool-calling)
- [ ] 6.5 Archive the change
- [ ] 6.6 Commit and push to repository
