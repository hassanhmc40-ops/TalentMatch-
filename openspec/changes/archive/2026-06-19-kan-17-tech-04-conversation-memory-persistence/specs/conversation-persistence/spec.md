## MODIFIED Requirements

### Requirement: Conversation persistence uses SDK middleware pipeline

Conversation persistence SHALL be handled by the `RememberConversation` middleware, which is auto-registered by the `GeneratesText` trait when the agent uses `RemembersConversations` and has a conversation participant. The middleware SHALL use `DatabaseConversationStore` for all read/write operations against the `agent_conversations` and `agent_conversation_messages` tables.

#### Scenario: Middleware is auto-registered for conversation agents
- **WHEN** an agent uses the `RemembersConversations` trait
- **AND** `hasConversationParticipant()` returns true (via `forUser()` or `continue()`)
- **THEN** the `GeneratesText` trait SHALL add `RememberConversation` to the middleware pipeline

#### Scenario: New conversation is auto-created when no ID is set
- **WHEN** `prompt()` is called on an agent with a participant but no `currentConversation()`
- **THEN** the middleware SHALL call `DatabaseConversationStore::storeConversation()` with a UUID7
- **AND** SHALL insert a row in `agent_conversations`
- **AND** SHALL set `currentConversation()` on the agent to the new ID
- **AND** SHALL store the user message and assistant response in `agent_conversation_messages`

#### Scenario: Existing conversation is continued with provided ID
- **WHEN** `$agent->continue($conversationId, $user)` is called before `prompt()`
- **THEN** `currentConversation()` SHALL return the provided ID
- **AND** the middleware SHALL use that ID for all message storage
- **AND** SHALL NOT create a new conversation row

#### Scenario: User message is stored
- **WHEN** a user sends a message via `prompt()`
- **THEN** `DatabaseConversationStore::storeUserMessage()` SHALL insert a row in `agent_conversation_messages` with `role = 'user'`, the message content, and a UUID7 ID

#### Scenario: Assistant message is stored
- **WHEN** the agent responds via `prompt()`
- **THEN** `DatabaseConversationStore::storeAssistantMessage()` SHALL insert a row in `agent_conversation_messages` with `role = 'assistant'`, the response text, tool calls, tool results, usage, and meta

#### Scenario: Conversation updated_at is touched on each message
- **WHEN** a user or assistant message is stored
- **THEN** the `updated_at` column of the conversation row SHALL be updated to the current timestamp

### Requirement: Conversation table names are configurable

The table names for conversations and messages SHALL be configurable via `config('ai.conversations.tables.conversations')` and `config('ai.conversations.tables.messages')`, with env variable overrides `AI_CONVERSATIONS_TABLE` and `AI_CONVERSATION_MESSAGES_TABLE`.

#### Scenario: Table names default to SDK convention
- **WHEN** no env overrides are set
- **THEN** `config('ai.conversations.tables.conversations')` SHALL return `'agent_conversations'`
- **AND** `config('ai.conversations.tables.messages')` SHALL return `'agent_conversation_messages'`

#### Scenario: Table names are overridable via env
- **WHEN** `AI_CONVERSATIONS_TABLE` is set
- **THEN** `config('ai.conversations.tables.conversations')` SHALL return the env value
- **AND** `DatabaseConversationStore::conversationsTable()` SHALL use the configured value

### Requirement: Conversation title can be auto-generated

The `RememberConversation` middleware SHALL generate a conversation title from the first user message when creating a new conversation.

#### Scenario: Title is generated from first message
- **WHEN** a new conversation is created
- **THEN** the middleware SHALL call the AI to generate a 3-5 word title from the user's first message
- **AND** the title SHALL be stored in the `title` column of `agent_conversations`

#### Scenario: Title generation can be disabled
- **WHEN** `config('ai.conversations.generate_title')` is `false`
- **THEN** the middleware SHALL fall back to truncating the first message to 50 characters

### Requirement: Latest messages are loaded for conversation context

When an existing conversation is continued, the middleware SHALL load the latest messages to provide context to the agent.

#### Scenario: Messages are loaded in chronological order
- **WHEN** an agent is prompted with an existing conversation ID
- **THEN** `DatabaseConversationStore::getLatestConversationMessages()` SHALL retrieve messages ordered by creation timestamp
- **AND** the messages SHALL include both user and assistant messages
- **AND** the messages SHALL include tool call and tool result entries when present

#### Scenario: Message limit is applied
- **WHEN** a conversation has more messages than the configured limit
- **THEN** only the latest N messages SHALL be loaded
- **AND** N SHALL be configurable via `maxConversationMessages()` on the agent (default: 100)
