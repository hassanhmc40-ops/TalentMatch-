## Why

HR users have no way to browse past conversations with the assistant. Each conversation is only accessible from the specific analysis page it was started on. This makes it impossible to revisit earlier candidate discussions, compare notes across sessions, or pick up where they left off.

## What Changes

- Add a "Conversations récentes" section to the dashboard showing the user's recent conversations
- Each conversation entry displays: candidate name, offer title, conversation title, message count, last message preview, and relative date
- Users can search/filter conversations by candidate name, offer title, or date range
- Clicking a conversation opens the chat interface with full context (candidate, offer, analysis) preserved
- Conversations without a linked `candidate_analysis_id` are omitted (no context to restore)

## Capabilities

### New Capabilities
- `conversation-history`: Dashboard section for browsing, searching, and filtering past conversations with context retention

### Modified Capabilities

None — no requirement changes to existing specs.

## Impact

- `app/Models/AgentConversation.php` — add scope(s) for latest message and search filtering
- `app/Http/Controllers/DashboardController.php` — load recent conversations with eager-loaded relationships
- `resources/views/dashboard.blade.php` — new conversations card with Alpine.js search/filter
- No new routes, no database migrations, no model table changes
