## Why

The `ConversationalAgent` and its three tools (`GetCandidateAnalysis`, `GetJobRequirements`, `CompareCandidates`) exist but have no UI, no proper conversation lifecycle, English-only instructions, and no authorization scoping. HR agents need a chat interface where they can ask natural-language questions about candidates, get tool-grounded answers, ask follow-ups with memory, and see comparisons — all in French with proper access control.

## What Changes

- Rewrite `ConversationalAgent::instructions()` from English to French with explicit tool-usage rules
- Add authorization checks in all three tools: scope queries to the authenticated user's offers
- Fix `GetJobRequirements` tool: replace `min_years_experience` with `min_experience_years`
- Create chat UI: conversation view with message history, auto-scroll, loading state
- Add route and controller for conversation start and follow-up messages
- Wire conversation memory persistence (laravel/ai SDK memory tables)
- Ensure the agent rejects questions about candidates the user does not own

## Capabilities

### New Capabilities
- `conversational-agent`: Conversational agent architecture including French-language HR assistant with tool calling, conversation memory, authorization-scoped tools, and chat UI.

### Modified Capabilities
- (none — existing conversational agent code has no spec yet)

## Impact

- `app/Ai/Agents/ConversationalAgent.php`: Rewrite instructions in French
- `app/Ai/Tools/GetCandidateAnalysis.php`: Add authorization scope (user's offers only)
- `app/Ai/Tools/GetJobRequirements.php`: Fix column name, add scope
- `app/Ai/Tools/CompareCandidates.php`: Add authorization scope, same-offer validation
- New chat UI: `resources/views/conversations/show.blade.php` and shared components
- New controller: `App\Http\Controllers\ConversationController`
- New route: `GET/POST /conversations/{offre}/{candidat}`
- `tests/Feature/ConversationalAgentTest.php`: Feature tests for conversation flow
