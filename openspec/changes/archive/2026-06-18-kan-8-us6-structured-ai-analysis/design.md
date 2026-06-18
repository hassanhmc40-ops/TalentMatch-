## Context

The AI analysis layer is already implemented across several classes:

- `CvAnalysisAgent` — uses Laravel AI SDK's `HasStructuredOutput` and `Promptable` traits; defines `instructions()` (system prompt) and `schema()` (JSON schema).
- `AnalyseCvJob` — handles the end-to-end flow: builds prompt, calls agent, validates, persists.
- `ValidateStructuredAnalysis` — validates the AI response against all field rules (types, ranges, enum).
- `PersistValidatedAnalysis` — persists validated data with mapped English column names.

Current state: the flow works end-to-end but lacks formal spec documentation and targeted test coverage for the prompt and agent call itself.

## Goals / Non-Goals

**Goals:**
- Document the prompt strategy: French-language system instructions + user prompt built from CV text and job offer
- Document the structured output schema with all field types and constraints
- Document the integration points between agent, job, validation, and persistence
- Write tests for the prompt content, agent schema, and integration flow

**Non-Goals:**
- Do not change the existing validation or persistence logic
- Do not add new fields to the structured output contract
- Do not change the queue job dispatch or retry behavior
- Do not modify the AI model or provider configuration

## Decisions

| Decision | Choice | Rationale |
|----------|--------|-----------|
| Prompt language | French | HR agents work in French; AI outputs match the domain language |
| Structured output | Laravel AI `HasStructuredOutput` via `schema()` | Type-safe, declarative, integrates with agent's prompt() method |
| System instructions | `instructions()` method on agent | Clear separation of system context from user prompt |
| Field naming in AI response | French (e.g. `competences_extraites`) | Natural for French-language AI; mapping to English DB columns happens in validation layer |
| Flow orchestration | `AnalyseCvJob::handle()` | Single job handles agent call → validation → persistence in one unit of work |

## Sequence

```
AnalyseCvJob::handle()
  │
  ├─ Candidate::findOrFail()
  ├─ JobOffer::findOrFail()
  │
  ├─ Build French prompt (CV text + offer criteria)
  │
  ├─ CvAnalysisAgent::make()
  │   └─ agent->prompt($prompt)
  │       └─ [AI returns structured JSON]
  │
  ├─ ValidateStructuredAnalysis::validate($aiResponse)
  │   ├─ [pass] → mapped data returned
  │   └─ [fail] → throw ValidationFailedException, job fails with failed status
  │
  └─ PersistValidatedAnalysis::persist($mappedData, $jobOfferId, $candidateId)
      └─ CandidateAnalysis::create() with status=completed
```

## Risks / Trade-offs

| Risk | Mitigation |
|------|------------|
| AI returns French field names; DB uses English | Mapping is handled in `ValidateStructuredAnalysis::FRENCH_TO_ENGLISH` mapping table |
| AI returns invalid JSON or missing fields | `ValidateStructuredAnalysis` rejects with French error; job fails; status set to `failed` |
| AI returns out-of-range values | All fields validated with min/max/enum constraints before persistence |
| Prompt changes could break structured output | The `schema()` method defines the contract; AI must conform to the JSON schema |
