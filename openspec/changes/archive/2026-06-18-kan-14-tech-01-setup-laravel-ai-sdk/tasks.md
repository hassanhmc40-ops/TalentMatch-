## 1. Package Installation & Configuration

- [x] 1.1 Run `composer require laravel/ai` to install the SDK
- [x] 1.2 Run `php artisan vendor:publish --provider="Laravel\Ai\AiServiceProvider"` to publish config and migrations
- [x] 1.3 Run `php artisan migrate` to create `agent_conversations` and `agent_conversation_messages` tables
- [x] 1.4 Create `config/ai.php` with OpenAI as default provider, Anthropic as secondary, and custom base URL support
- [x] 1.5 Configure `.env.example` with `OPENAI_API_KEY`, `OPENAI_ORGANIZATION`, `ANTHROPIC_API_KEY`, and default model vars (commented out)
- [x] 1.6 Verify `config/ai.php` loads without errors via `php artisan config:show ai`

## 2. Agents Directory & Foundation

- [x] 2.1 Create `app/Ai/Agents/` and `app/Ai/Tools/` directories
- [x] 2.2 Create `app/Ai/Agents/CvAnalysisAgent.php` implementing `Agent` and `HasStructuredOutput` with the full CV analysis JSON schema
- [x] 2.3 Create `app/Ai/Agents/ConversationalAgent.php` implementing `Agent` and `Conversational` using `RemembersConversations` trait
- [x] 2.4 Add `HasConversations` trait to `User` model

## 3. Tool Classes

- [x] 3.1 Run `php artisan make:tool GetCandidateAnalysis` — tool retrieves full candidate analysis by ID
- [x] 3.2 Run `php artisan make:tool GetJobRequirements` — tool retrieves job offer criteria by ID
- [x] 3.3 Run `php artisan make:tool CompareCandidates` — tool compares two candidate analyses for the same offer

## 4. Testing

- [x] 4.1 Create `tests/Feature/AiSdkSetupTest.php` with Pest — verify package installed, config published, tables exist
- [x] 4.2 Create `tests/Unit/CvAnalysisAgentTest.php` — verify structured output schema matches contract
- [x] 4.3 Create `tests/Unit/ConversationalAgentTest.php` — verify conversational interface and `RemembersConversations` trait
- [x] 4.4 Create `tests/Unit/ToolsTest.php` — verify tool classes exist with correct schemas and descriptions
- [x] 4.5 Create `tests/Unit/UserModelTest.php` — verify `HasConversations` trait is applied

## 5. Code Quality & Verification

- [x] 5.1 Run `vendor/bin/pint --format agent`
- [x] 5.2 Run `php artisan test --compact --filter=AiSdkSetup` — 6/6 passed
- [x] 5.3 Run `php artisan test --compact` — 89/89 passed (0 regressions)
- [x] 5.4 No debug/dd/dump statements remain

## 6. QA Checklist

- [x] 6.1 `php artisan config:show ai` — config loads with models section (gpt-4o-mini, text-embedding-3-small)
- [x] 6.2 All 5 AI classes autoload correctly (verified via tinker)
- [x] 6.3 `agent_conversations` and `agent_conversation_messages` tables exist
- [x] 6.4 `app/Ai/Agents/` and `app/Ai/Tools/` directories exist with all 5 expected classes
- [x] 6.5 Not applicable (no debugbar for this infra change)
