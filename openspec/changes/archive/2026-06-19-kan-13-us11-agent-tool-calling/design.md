## Context

Three AI tools exist (GetCandidateAnalysis, GetJobRequirements, CompareCandidates) registered on the ConversationalAgent. The agent implements `HasTools` but not `HasMiddleware`. The SDK v0.8.1 `executeTool()` uses `try/finally` without `try/catch`, so tool exceptions propagate unhandled — contradicting the existing spec R9. Tool names default to PascalCase class names via `ToolNameResolver`. The streaming SSE endpoint (`/stream`) only emits `TextDelta` events; tool call invocations during streaming are invisible to the user.

## Goals / Non-Goals

**Goals:**
- Add explicit `name()` method to all three tools returning snake_case names (`get_candidate_analysis`, `get_job_requirements`, `compare_candidates`)
- Implement `HasMiddleware` on `ConversationalAgent` with a `LogToolCalls` middleware for audit logging
- Wrap tool execution in a try/catch so exceptions are returned as tool results to the model
- Add a `ToolExecution` model + migration for persistent audit trail
- Show tool call status in the streaming chat UI (inline indicator during invocation)
- Measure and report tool execution duration

**Non-Goals:**
- No changes to the SDK vendor code — SDK gaps are worked around in application code
- No tool result caching (out of scope for this change)
- No tool-level rate limiting (out of scope for this change)
- No changes to existing tool authorization logic (already working)

## Decisions

1. **Wrapper pattern for exception handling** over SDK fork.
   - *Why:* The SDK `executeTool()` lacks a catch block, but we can't modify vendor code. Instead, each tool's `handle()` wraps its logic in try/catch and returns a French error string on failure. This is simpler than overriding the SDK pipeline and doesn't require forking.

2. **`LogToolCalls` middleware** implementing `HasMiddleware` on `ConversationalAgent`.
   - *Why:* The SDK's `HasMiddleware` contract allows the agent to inject middleware into the prompt pipeline. A `LogToolCalls` middleware listens to `InvokingTool` and `ToolInvoked` events dispatched by the SDK and persists records to the `tool_executions` table.
   - *Alternative considered:* Event subscribers or model observers. Rejected — middleware is the SDK-native extension point and keeps the logging inline with the execution flow.

3. **Tool execution audit trail** via a new `ToolExecution` model.
   - Fields: `id`, `conversation_message_id` (FK to `agent_conversation_messages`), `tool_name`, `arguments` (JSON), `result_summary` (text, truncated), `duration_ms` (integer), `success` (boolean), `error_message` (nullable text), `created_at`.
   - *Why:* The existing `agent_conversation_messages.tool_calls` and `tool_results` columns store raw data, but querying them for audit/analytics is cumbersome. A dedicated table with structured fields is more queryable.

4. **Streaming tool call indicator** via SSE events.
   - *Why:* The SDK dispatches `InvokingTool` and `ToolInvoked` events during tool execution. We can listen to these and emit SSE events with tool name and status ("loading..." / "done") that the frontend renders as inline badges.
   - *Alternative considered:* Hiding tool calls entirely. Rejected — transparency builds user trust and shows the agent is working.

## Risks / Trade-offs

- [SDK version] If the SDK adds its own try/catch in a future version, our wrapper pattern becomes redundant but not harmful (double catch is safe). Mitigation: monitor SDK releases.
- [Performance] The `LogToolCalls` middleware adds a DB write per tool execution. Mitigation: use `withoutTimestamps()` on the model to skip updated_at writes; batch inserts are possible if performance becomes an issue.
- [Streaming complexity] Emitting tool call SSE events adds client-side complexity. Mitigation: keep the frontend changes minimal — a single "🔍 Analyse en cours..." badge that appears/disappears.
