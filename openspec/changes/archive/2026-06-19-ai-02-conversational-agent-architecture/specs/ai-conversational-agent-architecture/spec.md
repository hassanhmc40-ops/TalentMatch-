## ADDED Requirements

### Requirement: Tool classes implement a consistent contract

Each tool class SHALL live in `app/Ai/Tools/`, implement `Laravel\Ai\Contracts\Tool`, and define a `handle(Request $request): string` method that returns a plain-text French message.

#### Scenario: Tool returns authorization error for unauthorized user
- **WHEN** the tool's `handle()` method is called with an ID belonging to another user's resource
- **THEN** it SHALL return a French error message
- **AND** SHALL NOT throw an exception

#### Scenario: Tool returns data for authorized user
- **WHEN** the tool's `handle()` method is called with an authorized ID
- **THEN** it SHALL return formatted data as a string
- **AND** the response SHALL be in French

### Requirement: Tools are registered via the agent's tools() method

`ConversationalAgent::tools()` SHALL return an iterable of instantiated tool objects. The SDK SHALL serialize these into the OpenAI function-calling format when building the request body.

#### Scenario: Tools are registered and serialized
- **WHEN** inspecting `ConversationalAgent::tools()`
- **THEN** it SHALL return at least three tools
- **AND** each tool SHALL be an instance of `Laravel\Ai\Contracts\Tool`

### Requirement: Authorization is performed inside tool handle()

Each tool SHALL perform authorization inside its `handle()` method using `auth()->id()` and Eloquent scoped queries. Tools SHALL NOT trust the caller to pass pre-authorized IDs.

#### Scenario: Unauthorized return is silent (no exception)
- **WHEN** authorization fails inside `handle()`
- **THEN** the tool SHALL return a string error message
- **AND** SHALL NOT throw or log a security exception in the UI

### Requirement: Memory uses the SDK middleware pipeline

Conversation persistence SHALL be handled by the `RememberConversation` middleware registered via `Ai::routeMiddleware()`. The middleware SHALL check `$agent->currentConversation()` to decide whether to create a new conversation or continue an existing one.

#### Scenario: New conversation is auto-created
- **WHEN** an agent is prompted without a pre-set conversation ID
- **THEN** the middleware SHALL call `storeConversation()` which generates a UUID7
- **AND** SHALL insert a new row in `agent_conversations`
- **AND** SHALL store the user message and assistant response

#### Scenario: Existing conversation is continued
- **WHEN** an agent is prompted with a pre-set conversation ID via `continue()`
- **THEN** the middleware SHALL NOT create a new conversation
- **AND** SHALL store messages in the existing conversation

#### Scenario: Conversation messages are limited
- **WHEN** a conversation exceeds the maximum message count
- **THEN** the middleware SHALL retrieve only the latest N messages
- **AND** N SHALL be configurable via `maxConversationMessages()`

### Requirement: Agent follows a predictable lifecycle

The agent SHALL follow the lifecycle: `make()` → `continue()` or `forUser()` → `prompt()` or `stream()` or `queue()`. Each method SHALL return the appropriate response type.

#### Scenario: Agent is instantiated via make()
- **WHEN** `ConversationalAgent::make()` is called
- **THEN** it SHALL return a new agent instance via the container

#### Scenario: Conversation context is set via continue()
- **WHEN** `$agent->continue($conversationId, $user)` is called
- **THEN** `currentConversation()` SHALL return the provided ID
- **AND** subsequent `prompt()` calls SHALL use this conversation

#### Scenario: Agent is prompted with tools
- **WHEN** `$agent->prompt($message)` is called
- **THEN** it SHALL send the prompt with the agent's instructions and tools to the provider
- **AND** SHALL return an `AgentResponse`

### Requirement: Tool execution errors are handled gracefully

If the AI invokes a tool that throws an exception, the agent SHALL return the error to the model without crashing the entire request.

#### Scenario: Tool exception is returned to model
- **WHEN** a tool's `handle()` throws an exception
- **THEN** the SDK SHALL catch the exception
- **AND** SHALL return the error message to the model as a tool result
- **AND** the overall request SHALL NOT fail

#### Scenario: Empty tool result is handled
- **WHEN** a tool returns an empty or null result
- **THEN** the agent SHALL inform the user the data was not found
- **AND** SHALL NOT hallucinate substitute data
