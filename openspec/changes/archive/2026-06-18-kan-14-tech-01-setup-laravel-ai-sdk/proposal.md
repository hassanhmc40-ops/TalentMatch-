## Why

TalentMatch requires two AI layers: structured CV analysis (Layer 1) and a conversational agent with tools and memory (Layer 2). The Laravel AI SDK (`laravel/ai`) provides the unified API, provider abstraction, structured output support, agent framework, and conversation memory needed to build both layers. Currently, no AI SDK is installed — this change lays the foundation before any AI feature can be implemented.

## What Changes

- Install `laravel/ai` Composer package
- Publish and run AI SDK migrations (`agent_conversations` + `agent_conversation_messages` tables)
- Create `config/ai.php` with provider and model configuration
- Configure `.env` with AI provider API keys (OpenAI as default)
- Create base `app/Ai/Agents/` directory structure with foundation agent class
- Create base `app/Ai/Tools/` directory structure with foundation tool class
- Create `app/Ai/Agents/CvAnalysisAgent.php` with structured output support (Layer 1 contract)
- Create `app/Ai/Agents/ConversationalAgent.php` with conversation memory (Layer 2)
- Add `HasConversations` trait to `User` model
- Add Laravel AI SDK service provider registration

## Capabilities

### New Capabilities

- `ai-sdk-installation`: Package install, config publish, migration run, and service provider registration
- `ai-sdk-provider-configuration`: Provider setup (OpenAI default) with `.env` key configuration and custom base URL support
- `ai-sdk-model-configuration`: Default model configuration for text generation and embeddings
- `ai-sdk-structured-output`: Schema builder integration and `HasStructuredOutput` contract support for CV analysis agent
- `ai-sdk-agent-foundation`: Base agent pattern, `app/Ai/` directory, tool system, `Conversational` interface with `RemembersConversations` trait

### Modified Capabilities

(none)

## Impact

- **New Composer dependency**: `laravel/ai`
- **New config file**: `config/ai.php`
- **New `.env` variables**: `OPENAI_API_KEY`, `OPENAI_ORGANIZATION`, default model settings
- **New migrations**: `agent_conversations` and `agent_conversation_messages` tables
- **New directory**: `app/Ai/` with `Agents/` and `Tools/` subdirectories
- **Modified model**: `User` model gains `HasConversations` trait
- **Modified service provider**: `AppServiceProvider` or dedicated `AiServiceProvider` for binding
