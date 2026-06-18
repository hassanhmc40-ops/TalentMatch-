## Why

KAN-8 US6 is the core AI analysis user story: when a candidate CV is submitted against a job offer, the system must call the AI with a strict JSON schema, validate the structured response, and persist the extracted data. While the validation and persistence layers already exist as separate specs, there is no overarching spec tying the full AI analysis flow together — the prompt strategy, agent setup, structured output contract, and integration with the queue job.

## What Changes

- Formalize the `CvAnalysisAgent` prompt strategy (French-language instructions, structured schema)
- Formalize the structured output contract (the JSON schema with all required fields)
- Define how `AnalyseCvJob` integrates agent call → validation → persistence
- No new code is needed (agent, validation, persistence already exist); this change formalizes the spec and ensures test coverage

## Capabilities

### New Capabilities
- `ai-analysis`: The end-to-end structured AI analysis flow: agent setup, prompt strategy, structured output contract, integration with queue job

### Modified Capabilities
- `analysis-persistence`: No requirement changes (already covers persistence)
- `structured-analysis-validation`: No requirement changes (already covers validation)
- `queue-jobs`: No requirement changes (already covers job dispatch)

## Impact

- **New files**: None (code already exists)
- **Modified files**: `app/Ai/Agents/CvAnalysisAgent.php` (may be refined), `app/Jobs/AnalyseCvJob.php` (already updated with retry)
- **Tests**: Existing tests for agent, validation, persistence; may add integration tests
