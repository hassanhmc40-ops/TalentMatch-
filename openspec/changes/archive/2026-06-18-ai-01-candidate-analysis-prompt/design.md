## Context

The `CvAnalysisAgent` currently returns French system instructions and a raw prompt built from CV text and offer data. There is no explicit prompt strategy — no anti-hallucination clauses, no few-shot examples, no reasoning chain for score calculation. The `laravel/ai` SDK supports structured output via `HasStructuredOutput` trait which enforces the JSON schema at the API level, but the prompt content itself is not optimized for consistency.

## Goals / Non-Goals

**Goals:**
- Define a structured system prompt in French with role, task, format rules, and constraints
- Add few-shot examples to make score calculation and recommendation reproducible
- Add explicit anti-hallucination guardrails (e.g., "if not in CV, do not invent")
- Ensure deterministic output across repeated calls with identical input
- All prompt logic lives in `CvAnalysisAgent` (no new service classes)

**Non-Goals:**
- No changes to the JSON schema or structured output contract
- No new database tables or migrations
- No changes to the queue/job infrastructure
- No changes to the validation layer (`ValidateStructuredAnalysis`)

## Decisions

1. **System prompt as a dedicated method with sections** — The `instructions()` method will return a multi-section prompt with clear delimiters: role definition, task description, field-by-field rules, score calculation logic, anti-hallucination guard, and recommendation criteria. Each section is separated by blank lines for readability and token efficiency.

2. **Few-shot examples embedded in the prompt** — 1-2 complete worked examples of CV + offer → JSON output will be appended to `prompt()`. These demonstrate the expected scoring logic and recommendation reasoning. Chosen over a separate "examples" file because keeping them close to the agent avoids drift.

3. **Anti-hallucination via explicit rules, not temperature tuning** — The system prompt will include "Ne pas inventer" clauses per field. Temperature stays at the SDK default (0.7 for creative, but structured output enforcement via schema mitigates variance). We do not override temperature because structured output + deterministic prompt content is sufficient.

4. **Score calculation described as weighted reasoning, not a formula** — The prompt describes the criteria (skills match, experience adequacy, language fit, education relevance) and asks the AI to reason step by step before outputting a score. This is more robust than hardcoding weights because the AI can adapt to context.

5. **Deterministic output via structured output enforcement + explicit field ordering** — The `HasStructuredOutput` trait already enforces JSON schema. Additionally, the prompt specifies exact field ordering and output format to reduce randomness. Repeated calls with identical input should produce identical structure.

## Risks / Trade-offs

- [Risk] Longer prompts increase token cost per analysis → Mitigation: System prompt is ~1KB, few-shot examples ~500B each. Total under 2KB, acceptable for occasional analysis calls.
- [Risk] AI may still hallucinate despite anti-hallucination clauses → Mitigation: Validation layer (`ValidateStructuredAnalysis`) catches schema violations. Additional tests verify guardrail phrases exist in the prompt.
- [Risk] Few-shot examples may bias the AI toward specific patterns → Mitigation: Examples are deliberately generic (different industry, different skill set). Monitored via test assertions.
- [Trade-off] Few-shot examples in prompt vs separate reference — In-prompt is simpler and keeps the agent self-contained. Trade-off is higher token usage per call.
