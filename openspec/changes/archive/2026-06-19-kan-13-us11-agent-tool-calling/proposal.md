## Why

The three AI tools (GetCandidateAnalysis, GetJobRequirements, CompareCandidates) exist and work, but they lack robust orchestration: tool names use PascalCase class names instead of snake_case, there is no middleware pipeline for logging or auditing tool calls, tool exceptions during execution propagate unhandled (violating the existing spec), and the streaming UI does not visualize when tools are being invoked. US11 (Assistant uses Laravel tools) must be hardened for production use with proper orchestration, observable execution, and error resilience.

## What Changes

- Add explicit `name()` method to all three tools returning snake_case names (`get_candidate_analysis`, `get_job_requirements`, `compare_candidates`)
- Implement `HasMiddleware` on `ConversationalAgent` with a tool-logging middleware that records tool invocations
- Add exception handling in tool execution so errors are caught and returned to the model as tool results (fixes spec R9 gap)
- Add tool call visualization in the streaming chat UI (show inline indicator when tools are being invoked)
- Add a `ToolExecution` model and migration for persistent tool audit trail

## Capabilities

### New Capabilities
- `agent-tool-calling`: Tool orchestration, execution hardening, middleware pipeline, audit trail, and streaming visualization

### Modified Capabilities
- `conversational-agent`: Update "Tool execution errors are returned to the model" requirement to reflect the implemented exception handling; add tool-naming convention requirement

## Impact

- `app/Ai/Tools/GetCandidateAnalysis.php` — add `name()` method
- `app/Ai/Tools/GetJobRequirements.php` — add `name()` method
- `app/Ai/Tools/CompareCandidates.php` — add `name()` method
- `app/Ai/Agents/ConversationalAgent.php` — implement `HasMiddleware`, add tool-logging middleware
- `app/Ai/Middleware/LogToolCalls.php` — new middleware class
- `app/Models/ToolExecution.php` — new model
- `database/migrations/XXXX_create_tool_executions_table.php` — new migration
- `resources/views/conversations/show.blade.php` — tool call visualization in streaming UI
- `vendor/laravel/ai` — SDK gap identified; add wrapper/fallback in tool execution path for exception handling
