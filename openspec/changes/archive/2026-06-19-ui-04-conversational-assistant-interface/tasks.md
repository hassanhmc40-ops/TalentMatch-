## 1. Message History Loading

- [x] 1.1 Update `ConversationController::show()` — load persisted messages from `AgentConversationMessage::where('conversation_id', $id)->orderBy('created_at')->get()` and pass as JSON to the view
- [x] 1.2 Update view's Alpine.js `x-init` — parse the server-provided messages JSON and populate the `messages` array on load

## 2. Chat Bubble Redesign

- [x] 2.1 Create assistant bubble template: left-aligned, briefcase SVG avatar, `bg-neutral-100` background, `rounded-2xl rounded-bl-sm`
- [x] 2.2 Create user bubble template: right-aligned, user SVG avatar, `bg-primary-600` text-white, `rounded-2xl rounded-br-sm`
- [x] 2.3 Add timestamp below each bubble: `HH:MM` for today, `DD/MM HH:MM` for older messages
- [x] 2.4 Add basic markdown rendering (bold, inline code, unordered lists) in assistant bubbles

## 3. AJAX Message Sending

- [x] 3.1 Replace form POST+redirect with `fetch()` POST to `conversations.store` route returning JSON
- [x] 3.2 On success, append assistant response to messages array without page reload
- [x] 3.3 On error, set message state to error with retry button
- [x] 3.4 Clear input and re-focus after sending

## 4. SSE Streaming

- [x] 4.1 Add streaming route `GET /conversations/{offre}/{candidat}/stream`
- [x] 4.2 Create `ConversationController::stream()` — call `$agent->stream()` and return SSE response with token-by-token output
- [x] 4.3 Connect Alpine.js `EventSource` to the streaming endpoint on message send
- [x] 4.4 Update assistant message bubble content incrementally as each SSE event arrives
- [x] 4.5 Handle SSE connection close and errors

## 5. Typing Indicator

- [x] 5.1 Create typing indicator template: assistant-style bubble with 3 animated bouncing dots
- [x] 5.2 Show indicator when `loading` is true and no stream has started
- [x] 5.3 Replace indicator with streaming content when first SSE token arrives

## 6. Message States

- [x] 6.1 Add state tracking per message: `sending`, `sent`, `delivered`, `error`
- [x] 6.2 Single checkmark icon for sent state (grey)
- [x] 6.3 Double checkmark icon for delivered state (primary colour)
- [x] 6.4 Error icon + "Échec d'envoi" + "Renvoyer" button for error state

## 7. Input Enhancements

- [x] 7.1 Replace single-line input with auto-resizing textarea (max 6 lines)
- [x] 7.2 Enter sends message, Shift+Enter / Ctrl+Enter inserts newline
- [x] 7.3 Add character counter when approaching 2000 character limit
- [x] 7.4 Disable input and show "Assistant répond..." hint during streaming

## 8. Auto-scroll & Scroll Anchor

- [x] 8.1 Scroll to bottom on new message and during streaming updates
- [x] 8.2 Add floating down-arrow button when user scrolls up
- [x] 8.3 Click button scrolls smoothly to bottom

## 9. Welcome Empty State

- [x] 9.1 Show centered welcome card when no messages exist
- [x] 9.2 Display candidate name and 3 suggested question buttons
- [x] 9.3 Clicking a suggested question sends it as the first message

## 10. Test Coverage

- [x] 10.1 Write test: messages are loaded from database on page render
- [x] 10.2 Write test: conversation page shows welcome state when no messages
- [x] 10.3 Write test: stream endpoint validation and authorization
- [x] 10.4 Write test: conversation does not leak other conversations' messages

## 11. Code Quality & Archive

- [x] 11.1 Run `vendor/bin/pint` for code formatting
- [x] 11.2 Run full test suite: `php artisan test --compact`
- [x] 11.3 Archive the change
- [x] 11.4 Commit and push to repository
