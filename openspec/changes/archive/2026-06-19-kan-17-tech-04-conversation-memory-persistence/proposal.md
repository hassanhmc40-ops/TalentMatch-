## Why

Conversation messages are already persisted by the SDK's `RememberConversation` middleware pipeline, but the session lifecycle (auto-creation via `forUser()`, continuation via `continue()`, message storage flow) and the `config/ai.php` conversations configuration are not formally documented. Formalizing them ensures consistent session handling across all agent interactions.

## What Changes

- Add session lifecycle scenarios to `conversation-persistence` spec: auto-creation, continuation, message storage, and the middleware pipeline
- Document `config/ai.php` conversations section (table names, title generation flag)
- Document the SDK's auto-loading of the conversation migration

## Capabilities

### New Capabilities
- (none — scope is covered by existing `conversation-persistence` capability)

### Modified Capabilities
- `conversation-persistence`: Add session lifecycle requirements (auto-create, continue, message storage), middleware pipeline documentation, and configuration documentation

## Impact

- `config/ai.php` — already exists, documenting its conversations section
- `vendor/laravel/ai/` — SDK auto-loads the migration and registers the middleware; documenting the behavior
- `app/Http/Controllers/ConversationController.php` — already uses `continue()`; documenting the lifecycle
