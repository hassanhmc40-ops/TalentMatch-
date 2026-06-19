## Purpose

Define the agent tool calling conventions for TalentMatch, including snake_case naming, exception handling, middleware pipeline, and streaming UI feedback.

## Requirements

### Requirement: Tool names use snake_case convention

Each tool SHALL declare an explicit `name()` method returning a snake_case string. The SDK's `ToolNameResolver` SHALL use this name for tool registration and API calls.

#### Scenario: GetCandidateAnalysis returns snake_case name
- **WHEN** `GetCandidateAnalysis::name()` is called
- **THEN** it SHALL return `get_candidate_analysis`

#### Scenario: GetJobRequirements returns snake_case name
- **WHEN** `GetJobRequirements::name()` is called
- **THEN** it SHALL return `get_job_requirements`

#### Scenario: CompareCandidates returns snake_case name
- **WHEN** `CompareCandidates::name()` is called
- **THEN** it SHALL return `compare_candidates`

### Requirement: Tool exceptions are caught and returned to the model

If a tool's `handle()` method throws an exception, the agent SHALL catch it and return a French error description to the model, allowing the assistant to respond gracefully.

#### Scenario: Tool exception is gracefully handled
- **WHEN** a tool throws an exception during execution
- **THEN** the error SHALL be caught and returned to the AI model as a tool result
- **AND** the conversation SHALL continue without an HTTP 500 error

#### Scenario: Empty tool result does not cause hallucination
- **WHEN** a tool returns data indicating no results found
- **THEN** the assistant SHALL inform the user truthfully
- **AND** SHALL NOT invent or guess data

### Requirement: Agent supports middleware pipeline

The `ConversationalAgent` SHALL implement `HasMiddleware` and SHALL return a middleware pipeline that includes tool-logging middleware.

#### Scenario: Agent returns middleware array
- **WHEN** inspecting `ConversationalAgent::middleware()`
- **THEN** it SHALL return an array containing at least one middleware instance

#### Scenario: Tool invocations are logged via middleware
- **WHEN** a tool is invoked
- **THEN** the middleware SHALL persist a record to the `tool_executions` table with tool name, arguments, success status, and duration

### Requirement: Streaming UI shows tool call status

When streaming, the chat UI SHALL display an inline indicator when the agent invokes a tool, showing the tool name and status.

#### Scenario: Tool call badge appears during streaming
- **WHEN** the agent invokes a tool during an SSE stream
- **THEN** a badge with the tool name and "En cours..." SHALL appear in the UI

#### Scenario: Tool call badge updates on completion
- **WHEN** the tool execution completes
- **THEN** the badge SHALL update to show "Terminé" with a checkmark
