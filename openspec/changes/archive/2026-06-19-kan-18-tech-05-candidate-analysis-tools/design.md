## Context

Three candidate analysis tools (`GetCandidateAnalysis`, `GetJobRequirements`, `CompareCandidates`) are already implemented in `app/Ai/Tools/` and registered in `ConversationalAgent::tools()`. They follow a consistent pattern — implement `Laravel\Ai\Contracts\Tool`, define `handle(Request $request): string`, perform authorization via `auth()->id()` inside `handle()`, and return French-language string responses. This change formalizes their contracts, error handling rules, and acceptance criteria as a standalone spec.

## Goals / Non-Goals

**Goals:**
- Formalize tool contracts: input parameters, output format, authorization rules, error messages
- Define acceptance criteria for all three tools: success, unauthorized, cross-offer, empty results
- Document error handling strategy (string returns, no exceptions for auth failures)
- Make the spec the source of truth for tool behavior

**Non-Goals:**
- No new tool implementations (tools already exist)
- No changes to tool signatures or return formats
- No changes to the conversational agent or other specs

## Decisions

1. **Separate spec vs. inline in conversational-agent**: A new `candidate-analysis-tools` capability keeps tool contracts independently documented, testable, and reusable across future agents. The `conversational-agent` spec references these tools by name; the detailed contract lives here.

2. **Authorization pattern**: Each tool performs its own auth inside `handle()` using `auth()->id()` + scoped Eloquent queries. This is the SDK-recommended pattern — tools are stateless and self-authorizing. No pre-processing middleware needed.

3. **Error handling**: Tools return French string error messages (not exceptions) for auth failures and not-found cases. The SDK catches runtime exceptions and returns them to the model. Both paths documented.

## Risks / Trade-offs

- [Duplicate authorization logic] → Each tool independently scopes queries; duplication is acceptable because each tool has distinct query logic. Future extraction into a shared trait would be a follow-up.
- [Tool contract coupling to agent] → If a new agent uses different input schemas, tool contracts may need extension. The current `Request` object is generic enough.
