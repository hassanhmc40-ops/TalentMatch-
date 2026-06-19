## Context

The conversation view currently uses Alpine.js with an empty `messages: []` array. Messages are persisted in `agent_conversation_messages` but never loaded for display. The form POST + redirect means the page fully reloads after each message, losing scroll position and creating a jarring UX.

The `ConversationalAgent` uses `RemembersConversations` which persists via SDK's `DatabaseConversationStore`. Messages have: `id`, `conversation_id`, `user_id`, `agent` (bool), `role`, `content`, `attachments`, `tool_calls`, `tool_results`, `usage`, `meta`.

## Goals / Non-Goals

**Goals:**
- Load persisted messages from `agent_conversation_messages` on page render, ordered by `created_at`
- Replace full-page POST+redirect with AJAX fetch to a JSON endpoint
- Add SSE streaming endpoint for token-by-token assistant responses
- Redesign chat bubbles: assistant (left, bg-neutral-100, briefcase icon), user (right, bg-primary-600, user icon), timestamps
- Add typing indicator with CSS dot animation
- Add message states: sent (single check), delivered (double check), error (red icon + retry button)
- Auto-scroll to bottom with scroll anchor button when user scrolls up
- Auto-resize textarea, Ctrl+Enter / Enter to send
- Welcome empty state with candidate context and suggested questions
- All interaction via Alpine.js (consistent with existing frontend patterns)

**Non-Goals:**
- No new database tables or migrations
- No changes to the AI agent, tools, or prompt
- No conversation list/index page (one conversation per candidate analysis)
- No file attachments or image upload in chat
- No voice input or audio responses
- No conversation renaming or deletion
- No push notifications for new messages

## Decisions

1. **Alpine.js + fetch over Livewire or Inertia** — The project already uses Alpine.js throughout the dashboard and layout. Introducing a new frontend framework for a single page is not justified. Alpine + fetch/SSE is sufficient for this chat UX.

2. **SSE over WebSocket** — SSE is simpler (single HTTP connection, no bidirectional protocol, native EventSource API) and fits the use case where only the server pushes tokens. The user sends messages via POST, the assistant streams back via SSE. No need for a WebSocket server.

3. **Server-side message loading** — The controller queries `AgentConversationMessage::where('conversation_id', $id)->orderBy('created_at')->get()` and passes them to the view as JSON via `$messages->toJson()`. Alpine's `x-init` parses and populates the `messages` array.

4. **Chat bubble design** — Each bubble is a flex row. Assistant: avatar (briefcase SVG in a neutral circle), bubble (rounded-2xl rounded-bl-sm, bg-neutral-100), timestamp below. User: bubble (rounded-2xl rounded-br-sm, bg-primary-600 text-white), avatar (user SVG in a primary circle), timestamp below. This mirrors the GPT-4o web interface pattern.

5. **Markdown rendering** — A simple regex-based markdown parser in JavaScript within Alpine (bold `**text**`, lists `- item`, code `` `code` ``). No external library dependency. If more rendering is needed, the lightweight `marked` library can be added later.

6. **Message states** — Client-side state tracking: `sending` (grey check, message not yet persisted), `sent` (single check, message persisted but no response yet), `delivered` (double check, response received), `error` (red icon, failed). States are managed via Alpine `x-data`.

7. **Typing indicator** — Three dots with CSS `animate-bounce` staggered delays. Rendered inside an assistant-style bubble. Shows when `loading` is true and disappears when the response starts streaming.

8. **Streaming endpoint** — A new `GET /conversations/{offre}/{candidat}/stream` route. The controller method creates the agent, calls `promptStream()` (if supported) and streams the response as SSE. Uses Laravel's streaming response or Symfony's StreamedResponse. Each chunk is a JSON object `{ token: "..." }` sent as `data: ...\n\n`.

## Risks / Trade-offs

- [SSE complexity] — SSE requires PHP output buffering management (`ob_flush()`, `flush()`) and may not work with all server configurations (e.g., Nginx buffering). Mitigation: add `header('X-Accel-Buffering: no')` and test with the development server first. Fallback: if SSE is not feasible, fall back to a single JSON response with the full message.
- [AJAX CSRF] — The fetch POST must include the CSRF token from the meta tag. Alpine reads it from `document.querySelector('meta[name="csrf-token"]')`.
- [Markdown in Alpine] — A full markdown parser in Alpine would be complex. The initial regex approach handles bold, lists, and inline code only. If users paste complex formatting, it renders as plain text. This is acceptable for HR analysis responses which are primarily plain text with occasional lists.
- [Loading time] — Loading all messages on page render could be slow for conversations with 100+ messages. Mitigation: the existing agent spec limits to 100 messages. The query is a simple indexed lookup on `conversation_id` + `created_at`, which is fast even at scale.
