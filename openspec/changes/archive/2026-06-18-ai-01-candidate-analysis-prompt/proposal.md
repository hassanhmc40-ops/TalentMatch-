## Why

The current `CvAnalysisAgent` uses basic French system instructions and a raw prompt built from CV text and offer data. The AI sometimes returns hallucinated skills, inconsistent scores, or non-deterministic outputs across repeated calls. A well-designed prompt strategy with explicit constraints, few-shot examples, and validation hooks is needed to make analysis outputs reliable, consistent, and auditable for HR decisions.

## What Changes

- Design a structured system prompt in French with explicit role, task, output format, and anti-hallucination instructions
- Add few-shot examples demonstrating correct score calculation and recommendation logic
- Introduce explicit formatting rules for each structured output field
- Add reasoning-chain instructions to make matching_score reproducible
- Implement hallucination guardrails: "if not in CV, do not invent" clauses
- Modify `CvAnalysisAgent::instructions()` to return the new refined system prompt
- Modify `AnalyseCvJob` prompt to include few-shot context alongside CV and offer data
- Add prompt validation tests: verify presence of anti-hallucination clauses, field-specific rules, and few-shot examples
- Add deterministic output tests: repeated calls with same input produce same structured result

## Capabilities

### New Capabilities
- `candidate-analysis-prompt`: Prompt strategy design for structured CV analysis, including system prompt, few-shot examples, hallucination guardrails, deterministic output rules, and acceptance criteria.

### Modified Capabilities
- `ai-analysis`: The "Prompt is built from candidate CV and job offer data" requirement SHALL be expanded to include specific prompt strategy requirements (system prompt structure, few-shot examples, hallucination prevention, deterministic output).

## Impact

- `app/Ai/Agents/CvAnalysisAgent.php`: `instructions()` and `prompt()` methods will be rewritten
- `app/Ai/Prompts/`: New directory for prompt templates if complexity grows
- `tests/Feature/AnalyseCvJobTest.php`: Additional prompt-content assertions
- `tests/Unit/CvAnalysisAgentTest.php`: Tests for instructions content, few-shot presence, anti-hallucination clauses
- No new models, migrations, or database changes
