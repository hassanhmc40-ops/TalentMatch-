## Why

The current chat interface at `/conversations/{offre}/{candidat}` has a critical UX gap: the Alpine `messages: []` array is always empty on page load because persisted messages are never fetched from the database. After each message, the form POSTs and redirects — but the chat remains empty. Users have no chat history visible, no message states, no typing indicator beyond a generic spinner, and no way to retry failed messages.

HR agents using TalentMatch need a fluid, ChatGPT-like conversational experience: visible message history, real-time streaming responses, clear message states (sending/delivered/error), and a professional chat UI with avatars, timestamps, and markdown rendering.

## What Changes

- **Server-side message history loading** — The conversation controller fetches persisted messages from `agent_conversation_messages` and passes them to the view as a JSON array
- **AJAX message sending** — Replace the form POST + redirect with a `fetch()` request. On success, the assistant's response is appended to the chat without a page reload
- **SSE streaming** — Add a streaming endpoint so the assistant's response appears token by token, like ChatGPT
- **ChatGPT-style message bubbles** — Left-aligned assistant bubbles with a briefcase avatar icon, right-aligned user bubbles with a user avatar icon, timestamps, and basic markdown rendering (bold, lists, code)
- **Typing indicator** — Animated dot sequence in an assistant bubble while waiting for response
- **Message states** — Each user message shows a subtle state indicator: single check (sent), double check (delivered), error icon with retry button
- **Welcome/empty state** — When no messages exist, show a centered welcome card with context about the candidate and suggested questions
- **Auto-scroll** — Smooth scroll to latest message; scroll anchor button when scrolled up
- **Input improvements** — Auto-resize textarea, Ctrl+Enter to send, send on Enter (no Shift), disabled state during streaming

## Capabilities

### New Capabilities
- `conversational-agent-interface`: ChatGPT-style chat UI with message history, streaming, typing indicator, message states, and markdown rendering

### Modified Capabilities
- `conversational-agent`: Update existing "Chat UI" requirement to reflect the enhanced interface; add streaming endpoint contract

## Impact

- `app/Http/Controllers/ConversationController.php` — update `show()` to load messages, add streaming endpoint method
- `routes/web.php` — add streaming route for SSE
- `resources/views/conversations/show.blade.php` — complete rewrite of chat UI with Alpine.js, fetch API, SSE event source
- `tests/Feature/ConversationInterfaceTest.php` — new tests for message history loading, message states, streaming, empty state, error handling
- `tests/Feature/ConversationalAgentTest.php` — update existing chat UI tests

No new database columns, models, migrations, or AI backend changes.
