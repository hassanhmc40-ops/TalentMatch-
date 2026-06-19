## Context

Conversation persistence is handled entirely by the Laravel AI SDK's built-in middleware pipeline. The `RemembersConversations` trait on `ConversationalAgent` triggers the `RememberConversation` middleware (auto-registered by `GeneratesText` when the agent has a conversation participant). The middleware uses `DatabaseConversationStore` to persist user and assistant messages to the `agent_conversations` and `agent_conversation_messages` tables. The `config/ai.php` file exposes table name configuration via the `conversations.tables` section. This change formally documents the full lifecycle, configuration, and acceptance criteria.

## Goals / Non-Goals

**Goals:**
- Document the session lifecycle: auto-creation via `forUser()`, continuation via `continue()`, message storage flow
- Document `config/ai.php` conversations section (table names, title generation flag)
- Document the `DatabaseConversationStore` methods and their insert behavior
- Add acceptance criteria covering lifecycle scenarios

**Non-Goals:**
- No new code or migration changes (everything is already implemented)
- No changes to the `ConversationController` or agent classes

## Decisions

1. **SDK-managed persistence**: All message storage is handled by the SDK middleware, not the application. The application only provides the `config/ai.php` configuration and the `candidate_analysis_id` post-prompt update. This follows the SDK's framework convention where the application configures and the SDK executes.

2. **Table name configuration**: The `config('ai.conversations.tables.*')` keys are documented with their env variable overrides, matching the SDK's `DatabaseConversationStore` consumption pattern.

3. **Title generation**: The middleware can optionally generate conversation titles via AI (enabled by default via `config('ai.conversations.generate_title', true)`). This is documented as an SDK feature with the note that it can be disabled.

## Risks / Trade-offs

- [SDK version coupling] → The middleware pipeline is in the SDK; version upgrades could change behavior. The spec documents current SDK behavior as the baseline.
- [Configuration drift] → if the SDK adds new config keys, they need to be documented. This is an ongoing maintenance concern, not a blocker.
