## 1. Middleware Pipeline Verification

- [x] 1.1 Verify `RememberConversation` middleware is auto-registered when agent uses `RemembersConversations` + has participant
- [x] 1.2 Verify `GeneratesText::gatherMiddlewareFor()` adds the middleware
- [x] 1.3 Verify `DatabaseConversationStore` is bound as singleton in `AiServiceProvider`

## 2. DatabaseConversationStore Verification

- [x] 2.1 Verify `storeConversation()` inserts with UUID7 as primary key
- [x] 2.2 Verify `storeUserMessage()` inserts with `role = 'user'`, `content`, and conversation FK
- [x] 2.3 Verify `storeAssistantMessage()` inserts with `role = 'assistant'`, response text, tool calls, tool results, usage, and meta
- [x] 2.4 Verify `getLatestConversationMessages()` retrieves messages ordered by creation, limited to N
- [x] 2.5 Verify `touchConversation()` updates `updated_at` after each message

## 3. Configuration Verification

- [x] 3.1 Verify `config/ai.php` has `conversations.tables.conversations` key
- [x] 3.2 Verify `config/ai.php` has `conversations.tables.messages` key
- [x] 3.3 Verify env overrides: `AI_CONVERSATIONS_TABLE`, `AI_CONVERSATION_MESSAGES_TABLE`

## 4. Test Coverage

- [x] 4.1 Write test: auto-creates conversation when no ID is set (agent lifecycle)
- [x] 4.2 Write test: continues existing conversation when ID is provided via `continue()`
- [x] 4.3 Write test: user message is stored with correct role and content
- [x] 4.4 Write test: assistant message is stored with tool calls and results
- [x] 4.5 Write test: config table names return correct defaults
- [x] 4.6 Write test: config table names respect env overrides

## 5. Code Quality & Spec Sync

- [x] 5.1 Run `vendor/bin/pint` for code formatting
- [x] 5.2 Run full test suite: `php artisan test --compact`
- [x] 5.3 Sync delta specs to main `conversation-persistence/spec.md`

## 6. Archive & Push

- [x] 6.1 Archive the change
- [x] 6.2 Commit and push to repository
