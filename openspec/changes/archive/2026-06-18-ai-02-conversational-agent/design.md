## Context

The `ConversationalAgent` exists with English instructions and three Laravel AI SDK tools. There is no UI, no conversation lifecycle management, and no authorization scoping. The `RemembersConversations` trait from the laravel/ai SDK handles persistence automatically when the agent is used through the SDK's conversation API.

## Goals / Non-Goals

**Goals:**
- French-language system instructions for the conversational agent with explicit tool-usage rules
- Authorization scoping in all three tools: only return data for offers owned by the authenticated user
- Chat UI with message history, auto-scroll, loading indicator, and error state
- Conversation lifecycle: start, follow-up, persist via laravel/ai SDK memory tables
- Route and controller for the conversation endpoint

**Non-Goals:**
- No new database tables or migrations (laravel/ai SDK provides memory tables)
- No real-time WebSockets or SSE
- No multi-turn conversation branching
- No conversation listing or history management UI

## Decisions

1. **Controller-per-view pattern** — `ConversationController` with `show` (GET: renders chat UI with message history) and `store` (POST: sends message, returns updated view). The `show` method loads existing messages from the SDK memory tables.

2. **Agent instantiation per request** — A new `ConversationalAgent` is created for each conversation interaction. The SDK's `RemembersConversations` trait loads conversation history automatically when the conversation ID is provided.

3. **Authorization in tools via injected user** — Each tool's `handle()` method receives the authenticated user through Laravel's service container or via a scoped query. Tools filter by `jobOffer.user_id` to enforce ownership.

4. **Chat UI with AlpineJS** — Simple Alpine-powered chat component: message list with auto-scroll, input form, loading spinner. No heavy frontend framework needed. Consistent with existing Alpine usage in the app.

5. **Matching score explanation tool** — A new `GetScoreExplanation` tool is added to provide natural-language reasoning for the matching_score. This helps the agent explain why a score was given.

## Risks / Trade-offs

- [Risk] Tools may be called with IDs the user does not own → Mitigation: Every tool scopes its query to the authenticated user's offers. Unauthorized access returns a French error message, not raw data.
- [Risk] Agent may hallucinate despite tool instructions → Mitigation: Instructions say "Never invent data. Always use tools." Tests verify tool-call assertions.
- [Risk] Long conversations may exceed token limits → Mitigation: laravel/ai SDK handles truncation via `RemembersConversations`. Acceptable for current scope.
