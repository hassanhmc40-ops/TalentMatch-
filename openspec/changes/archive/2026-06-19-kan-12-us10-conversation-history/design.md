## Context

Conversations are fully persisted in `agent_conversations` and `agent_conversation_messages` tables. Each conversation is linked to a `CandidateAnalysis` via `candidate_analysis_id`. The `byUser` scope exists on `AgentConversation` but is unused. There is no user-facing way to browse past conversations — they are only reachable from the specific analysis page they were started on.

The dashboard already loads KPI cards, score distribution, analysis status, and a recent analyses table. Adding a conversations section follows the same pattern.

## Goals / Non-Goals

**Goals:**
- Display the user's recent conversations on the dashboard with candidate name, offer title, conversation title, message count, last message preview, and relative date
- Provide client-side search/filter by candidate name, offer title, and date range
- Clicking a conversation navigates to the existing chat interface with full context preserved
- Conversations without `candidate_analysis_id` are excluded (no context to restore)

**Non-Goals:**
- No new database tables or migrations — existing schema is sufficient
- No new route or controller — history lives on the dashboard
- No server-side pagination for v1 (client-side filter on loaded data, matching the analyses pattern of 10 items)
- No conversation deletion or archiving from the history view

## Decisions

1. **Client-side search/filter with Alpine.js** over a dedicated API endpoint.
   - *Why:* The dashboard already uses Alpine.js for tab switching. Conversations are scoped to the authenticated user (max low hundreds). Client-side filtering avoids an extra API route and keeps the implementation simple. If performance becomes an issue later, a search endpoint can be added without changing the UI.
   - *Alternative considered:* Server-side search with a new `GET /api/conversations` endpoint. Rejected because it adds unnecessary complexity for v1.

2. **Eager-load `candidateAnalysis.candidate` and `candidateAnalysis.jobOffer`** in the `DashboardController`.
   - *Why:* Each conversation row needs the candidate name and offer title. Eager loading prevents N+1 queries.
   - *Alternative considered:* Adding a `withCandidateAndOffer` scope on `AgentConversation`. Rejected — eager loading in the controller is simpler and follows the existing dashboard pattern.

3. **Latest message via a `latestMessage` HasOne relationship** ordered by `created_at DESC`.
   - *Why:* The standard Eloquent pattern for "get the most recent related row." Provides the last message preview and relative date.
   - *Alternative considered:* Loading all messages and taking the last one in PHP. Rejected — wasteful for conversations with many messages.

4. **Relative date display** using `diffForHumans()` on `updated_at`.
   - *Why:* Laravel's built-in Carbon method produces French-friendly relative dates ("il y a 2 heures"). Consistent with UX expectations for a chat history view.

## Risks / Trade-offs

- [Performance] Client-side filtering loads all conversations in the view data. Mitigation: limit to 10 most recent conversations (matching the analyses section pattern).
- [Missing context] Some conversations may have null `candidate_analysis_id` (e.g., if the initial analysis link failed). Mitigation: exclude null entries from the query.
- [Stale data] The dashboard is cached for 5 minutes (KPI data). Mitigation: conversations section is loaded outside the KPI cache, so it reflects real-time data.
