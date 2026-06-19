## Why

The three candidate analysis tools (`GetCandidateAnalysis`, `GetJobRequirements`, `CompareCandidates`) exist and are functional, but lack a dedicated formal specification covering their contracts, error handling, authorization rules, and acceptance criteria. Formalizing them as a standalone capability makes the tool contracts explicit, testable, and reusable beyond the conversational agent.

## What Changes

- Create a new `candidate-analysis-tools` spec defining formal tool contracts, input/output schemas, error messages, and authorization rules.
- Define acceptance criteria for each tool covering success, unauthorized access, cross-offer comparison, and empty-results scenarios.
- The spec will serve as the single source of truth for tool behavior, referenced by the `conversational-agent` and `ai-conversational-agent-architecture` specs.

## Capabilities

### New Capabilities
- `candidate-analysis-tools`: Formal tool contracts, error handling rules, authorization scoping, and acceptance criteria for `GetCandidateAnalysis`, `GetJobRequirements`, and `CompareCandidates`.

### Modified Capabilities
- (none — existing specs reference tools as a detail; contracts move to the new capability)

## Impact

- `app/Ai/Tools/*.php` — these files are already implemented; the spec documents their current behavior.
- `openspec/specs/conversational-agent/spec.md` — MAY add a reference to `candidate-analysis-tools` as the source of tool contracts.
