## Purpose

TalentMatch relies on the Laravel AI SDK for all AI operations including structured CV analysis, tool-based conversational agents, and embedding/reranking features. This capability covers the base installation, provider configuration, and default model setup required before any AI feature can be used.

## Requirements

### Requirement: Laravel AI SDK is installed via Composer

The system SHALL install the `laravel/ai` package as a production dependency.

#### Scenario: Package is installed
- **WHEN** `composer require laravel/ai` is executed
- **THEN** the package SHALL be listed in `composer.json` under `require`
- **AND** the `Laravel\Ai\AiServiceProvider` SHALL be auto-discovered

#### Scenario: Service provider publishes config and migrations
- **WHEN** `php artisan vendor:publish --provider="Laravel\Ai\AiServiceProvider"` is executed
- **THEN** a `config/ai.php` file SHALL be created
- **AND** migration files for `agent_conversations` and `agent_conversation_messages` tables SHALL be published

#### Scenario: Conversation tables are migrated
- **WHEN** `php artisan migrate` is executed
- **THEN** the `agent_conversations` table SHALL exist in the database
- **AND** the `agent_conversation_messages` table SHALL exist in the database

### Requirement: AI providers are configurable

The system SHALL support multiple AI providers with OpenAI as the default.

#### Scenario: Default provider is OpenAI
- **WHEN** the application loads `config/ai.php`
- **THEN** the `default` provider SHALL be set to `openai`
- **AND** the `OPENAI_API_KEY` environment variable SHALL be used for authentication

#### Scenario: Secondary providers are available
- **WHEN** `config/ai.php` is inspected
- **THEN** Anthropic SHALL be configured as a secondary provider
- **AND** each provider SHALL support a custom `url` override via environment variable

#### Scenario: Environment variables are documented
- **WHEN** `.env.example` is inspected
- **THEN** it SHALL contain `OPENAI_API_KEY`, `OPENAI_ORGANIZATION`, `ANTHROPIC_API_KEY` variables (commented out)

### Requirement: Default models are configured for text generation and embeddings

The system SHALL define default models for text generation and embedding features.

#### Scenario: Default text model is configured
- **WHEN** the AI SDK performs a text generation request
- **THEN** the default model SHALL be `gpt-4o-mini`
- **AND** it SHALL be configurable via the `OPENAI_MODEL` environment variable

#### Scenario: Default embedding model is configured
- **WHEN** the AI SDK performs an embedding request
- **THEN** the default embedding model SHALL be `text-embedding-3-small`
- **AND** it SHALL be configurable via environment variable
