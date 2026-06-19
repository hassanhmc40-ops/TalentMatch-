## Why

US9 says: "HR user can ask natural language questions about an analyzed candidate."

The current conversational agent works, but has a critical UX gap: the agent has no awareness of which candidate the user is asking about. When a user arrives on the conversation page (`conversations/{offre}/{candidat}`), the page knows the candidate and offer context, but this context is never injected into the agent's system prompt. As a result:

- The agent cannot answer "What is the matching score?" without first asking "For which candidate ID?"
- Follow-up questions like "And what are their strengths?" lose the context of the previous turn
- The agent relies entirely on tool calls for every piece of information, even basic context that is already known from the page URL
- Tool calls require explicit IDs (`candidat_id`, `offre_id`) that the agent must either guess or ask for

This change injects the current candidate/offer context into the agent's instructions so the agent can answer questions immediately without asking for clarification, and tool calls auto-resolve the relevant IDs from the conversation context.

## What Changes

- **Context injection** — The `ConversationController` injects the current candidate name, analysis summary (score, recommendation, top skills), and offer title into the agent's system prompt via dynamic instructions
- **Auto-resolution of tool parameters** — The agent instructions tell it the current `candidat_id`, `offre_id`, and `analyse_id` so tool calls can be made without asking the user
- **Answer formatting** — The agent instructions specify structured answer formats for different question types: score questions, skills questions, comparison questions, recommendation questions
- **Follow-up question handling** — The agent is instructed that the user may ask follow-up questions without repeating the candidate name, and to use the previously established context
- **Out-of-scope handling** — The agent is instructed to politely decline questions unrelated to the candidate analysis (e.g., weather, general knowledge)

## Capabilities

### Modified Capabilities
- `conversational-agent`: Update agent instructions to use dynamic context injection; add auto-resolution of tool parameters from conversation context; add answer formatting rules; add out-of-scope handling

## Impact

- `app/Ai/Agents/ConversationalAgent.php` — refactor `instructions()` to accept dynamic context parameters (candidate name, analysis summary, offer title, IDs)
- `app/Http/Controllers/ConversationController.php` — pass analysis context to the agent before prompting
- `tests/Feature/ConversationalAgentTest.php` — add tests for context-injected prompts, follow-up question handling, out-of-scope question handling
- `tests/Feature/QuestionFlowTest.php` — new tests for question flow scenarios

No new database columns, models, migrations, tools, or UI changes.
