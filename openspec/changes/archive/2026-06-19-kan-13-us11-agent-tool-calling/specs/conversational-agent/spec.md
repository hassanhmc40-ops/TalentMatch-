## MODIFIED Requirements

### Requirement: Tool execution errors are returned to the model

If a tool's `handle()` method throws an exception, the SDK or agent SHALL catch it and return the error to the model without failing the request. Each tool SHALL wrap its `handle()` logic in a try/catch and return a French error message on failure.

#### Scenario: Tool exception is gracefully handled
- **WHEN** a tool throws an exception during execution
- **THEN** the error SHALL be caught
- **AND** the tool SHALL return a French error message as a string result
- **AND** the AI model SHALL receive the error as a tool result
- **AND** the overall HTTP request SHALL succeed

#### Scenario: Empty tool result does not cause hallucination
- **WHEN** a tool returns data indicating no results found
- **THEN** the assistant SHALL inform the user truthfully
- **AND** SHALL NOT invent or guess data

### Requirement: Each tool exposes a name() method

Each tool implementing the `Tool` contract SHALL also declare a `name()` method returning a snake_case identifier. This replaces the default `class_basename()` fallback from `ToolNameResolver`.

#### Scenario: Tool name is snake_case
- **WHEN** `name()` is called on any tool
- **THEN** it SHALL return a snake_case string matching the tool's function name convention
- **AND** the name SHALL match the name used in the system instructions for the corresponding tool
