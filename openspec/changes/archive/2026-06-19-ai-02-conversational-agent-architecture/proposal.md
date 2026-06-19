## Why

The existing conversational-agent spec defines high-level behavioral requirements (French instructions, three tools, conversation memory) but lacks detailed architecture specifications for tool registration, memory middleware pipeline, agent lifecycle, and edge-case handling. As the agent grows in complexity, a formal architecture spec is needed to ensure consistent tool error handling, conversation lifecycle management, and clear system boundaries.

## What Changes

- Replace the existing `conversational-agent` spec with a detailed architecture-focused version
- Add formal tool architecture: registration, input/output contracts, authorization, error responses
- Add formal memory architecture: middleware pipeline, conversation store, auto-generated IDs
- Add formal agent lifecycle: `make()` instantiation, conversation setup (`continue`/`forUser`), prompt dispatch, response handling, error recovery
- Add edge-case scenarios: tool failure recovery, empty responses, conversation overflow, concurrent conversations

## Capabilities

### New Capabilities
- `ai-conversational-agent-architecture`: Detailed architecture spec covering tool contracts, memory middleware, agent lifecycle, and acceptance criteria for the conversational agent.

### Modified Capabilities
- `conversational-agent`: Replace the entire spec with comprehensive architecture documentation covering tool architecture, memory architecture, agent lifecycle, and expanded acceptance criteria.

## Impact

- **specs**: `conversational-agent/spec.md` replaced with architecture-focused content
- **code**: No existing code changes needed — the current implementation already matches this architecture
- **tests**: Acceptance criteria expanded with additional edge-case scenarios; no existing tests need modification
- **docs**: Architecture details documented for future maintainers
