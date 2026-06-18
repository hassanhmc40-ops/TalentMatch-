## Context

TalentMatch needs AI capabilities for structured CV analysis (Layer 1) and a conversational assistant (Layer 2). The `laravel/ai` SDK provides a unified API across AI providers, agent classes with structured output, conversation memory via database tables, and a tool/function-calling system. Currently, no AI package is installed — no `config/ai.php`, no `app/Ai/` directory, no agent classes, no conversation tables.

The Laravel AI SDK v13 is the first-party package that integrates natively with Laravel's architecture. It publishes its own `AiServiceProvider`, migrations for `agent_conversations` and `agent_conversation_messages` tables, and a `config/ai.php` configuration file.

## Goals / Non-Goals

**Goals:**
- Install `laravel/ai` and register its service provider
- Publish and migrate conversation storage tables
- Create `config/ai.php` with OpenAI as default provider, plus Anthropic as secondary
- Create `app/Ai/Agents/CvAnalysisAgent.php` implementing `HasStructuredOutput` with the CV analysis JSON schema contract
- Create `app/Ai/Agents/ConversationalAgent.php` implementing `Conversational` via `RemembersConversations` trait
- Create `app/Ai/Tools/` with `GetCandidateAnalysis`, `GetJobRequirements`, and `CompareCandidates` tool stubs
- Add `HasConversations` trait to `User` model
- Configure `.env.example` with all AI SDK environment variables (commented out)
- Verify installation with a passing integration test

**Non-Goals:**
- Implementing the full CV analysis job/queue (deferred to KAN-15)
- Implementing the conversational assistant UI (deferred to US9)
- Wiring agents to routes or controllers
- Configuring embeddings, reranking, image, audio, or file features (will be added when needed)

## Decisions

1. **OpenAI as default provider** — OpenAI is the most widely supported provider with structured output (`response_format: json_schema`), tool calling, and broad model availability. Anthropic configured as secondary. Rationale: gpt-4o-mini offers the best cost/quality ratio for structured CV extraction.

2. **`CvAnalysisAgent` uses `HasStructuredOutput` not raw `JsonSchema` calls** — The SDK's `HasStructuredOutput` contract returns a `StructuredAgentResponse` accessible as an array, which maps directly to the CV analysis contract. Alternative considered: manual JSON parsing of raw text responses — rejected because structured output guarantees schema compliance at the API level.

3. **`ConversationalAgent` uses `RemembersConversations` trait** — The trait auto-loads conversation history from the `agent_conversations` table without implementing the `Conversational` interface manually. Alternative considered: custom `messages()` method — rejected because the trait handles storage and retrieval consistently.

4. **Tools as separate classes in `app/Ai/Tools/`** — The SDK's `make:tool` Artisan command scaffolds each tool with its own description, `handle` method, and input schema. Alternative: closures in the agent — rejected because tool classes are testable, reusable across agents, and follow the SDK convention.

5. **`HasConversations` on `User` model** — This trait provides the `conversations()` relationship and is required for `RemembersConversations` to associate conversations with users. This is the SDK's intended pattern.

6. **Separate agents for analysis vs. conversation** — CvAnalysisAgent is a one-shot structured extraction agent (no conversation needed); ConversationalAgent needs multi-turn memory and tools. Different interfaces (`HasStructuredOutput` vs `Conversational` + `HasTools`) justify separate classes.

## Risks / Trade-offs

- **[Missing API keys in .env]** → SDK will throw at runtime. Mitigation: `.env.example` documents all required keys; config has empty defaults so the app loads without crash.
- **[SDK version mismatch with Laravel]** → `composer require laravel/ai` resolves the compatible version automatically.
- **[Conversation table migrations conflict]** → SDK migration names are namespaced; no risk with existing app migrations.
- **[Agent design too abstract too early]** → The agents created here are thin wrappers around SDK contracts. If requirements change, contract implementations can be swapped without breaking callers.
