## Context

The conversational agent (`ConversationalAgent`) is already implemented at `app/Ai/Agents/ConversationalAgent.php` using the `laravel/ai` SDK. It uses the `Promptable` trait for agent instantiation and prompting, `RemembersConversations` for memory, and three tool classes for database retrieval. The SDK's `RememberConversation` middleware (registered via `Ai::routeMiddleware()`) handles automatic conversation persistence. This design documents the architecture that the current implementation follows.

## Goals / Non-Goals

**Goals:**
- Document the tool registration and execution architecture
- Document the memory middleware pipeline and data flow
- Document the full agent lifecycle from instantiation to response
- Define acceptance criteria for edge cases

**Non-Goals:**
- Changing any existing code or implementation
- Adding new tools or capabilities
- Modifying the SDK middleware or store

## Decisions

1. **Tool architecture: each tool is a standalone class implementing `Laravel\Ai\Contracts\Tool`**
   Tools live in `app/Ai/Tools/`. Each class defines `handle(Request): string` — accepts a `Request` DTO and returns a plain string. String return is dictated by the SDK contract. Authorization is performed inside `handle()` via `auth()->id()` and `whereHas('jobOffer', fn => where('user_id', $id))`.

2. **Tool registration via `tools()` method on the agent**
   `ConversationalAgent::tools()` returns an iterable of instantiated tool objects. The SDK serializes tool definitions into the OpenAI function-calling format when building request bodies. This happens inside `Promptable::prompt()` → `AgentPrompt` → provider gateway → `buildTextRequestBody()`.

3. **Memory architecture: SDK middleware pipeline**
   The `RememberConversation` middleware wraps every agent prompt. It checks `$agent->currentConversation()`. If null, it calls `storeConversation()` which generates a UUID7 and inserts a row into `agent_conversations`. Then it stores user and assistant messages in `agent_conversation_messages`. The middleware is registered via `Ai::routeMiddleware()` in the service provider.

4. **Conversation ID strategy: controller-managed via `continue()`**
   The `ConversationController` forces a predictable conversation ID (`candidate-analysis-{analysis->id}`) by calling `$agent->continue($conversationId, auth()->user())` before `prompt()`. This sets `$conversationId` on the agent, which the middleware detects (non-null) and skips conversation creation, using the provided ID for message storage.

5. **Agent lifecycle: make → continue → prompt**
   `ConversationalAgent::make()` (from `Promptable` trait) resolves via container. `continue()` sets conversation context. `prompt()` builds an `AgentPrompt` with instructions, messages, and tools, then calls the provider gateway which posts to OpenAI and returns an `AgentResponse`.

6. **System context: prepended to prompt**
   The controller builds a `$systemContext` string with candidate/offer context. In the current implementation this is set via `systemContext()`, which doesn't exist on the SDK. The context should be prepended to the prompt message string. This is documented as an existing gap.

## Risks / Trade-offs

- **[Existing system context gap]** The `systemContext()` call in the controller is a no-op method. Context must be passed differently (prepended to prompt). Mitigation: document this as a known gap; the agent's `instructions()` already includes role context.
- **[Conversation ID collision]** If two different analyses generate the same conversation ID pattern (unlikely with `candidate-analysis-{id}`), messages could interleave. Mitigation: the ID includes the analysis PK which is unique.
- **[SDK middleware coupling]** Memory depends on the `RememberConversation` middleware being registered. If removed, conversations won't persist. Mitigation: document as architectural invariant.
- **[Token limits]** Long conversations with many tool calls may hit model token limits. Mitigation: the SDK's `maxConversationMessages()` defaults to 100; agent instructions can set a lower limit.
