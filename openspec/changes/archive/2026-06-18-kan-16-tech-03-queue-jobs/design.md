## Context

AnalyseCvJob already exists and is dispatched on candidate submission. It handles:
- Loading Candidate and JobOffer models
- Building a French prompt with CV text and offer criteria
- Calling the AI via CvAnalysisAgent
- Validating the structured response via ValidateStructuredAnalysis
- Persisting via PersistValidatedAnalysis
- Manual retry logic (`$this->attempts() < 3`, `$this->release(30)`)

Current issues: retry relies on manual attempt tracking instead of Laravel's built-in properties; no `$tries` or `$backoff` declared; queue worker must be started manually.

## Goals / Non-Goals

**Goals:**
- Declare `$tries` and `$backoff` on AnalyseCvJob for declarative retry
- Replace manual `$this->attempts() < 3` guard with native Laravel retry
- Add `failed()` method as a safety net for unrecoverable failures
- Ensure `database` queue connection is configured and the `jobs` table migration exists
- Document the queue worker command and how to run it
- Write comprehensive tests covering dispatch, successful job, validation failure, transient failure, and retry exhaustion

**Non-Goals:**
- Do not change the queue driver (stays `database` for simplicity)
- Do not introduce Redis, SQS, or other queue backends
- Do not change the AI agent or validation/persistence logic
- Do not modify the submission controller flow

## Decisions

| Decision | Choice | Rationale |
|----------|--------|-----------|
| Retry mechanism | Laravel `$tries` + `$backoff` properties | More declarative and testable than manual `attempts()` checks; integrates with `php artisan queue:work` retry behavior |
| Failed job handler | `failed()` method on job | Keeps failure logic colocated with the job; avoids separate event listener for a single job type |
| Queue driver | `database` | Zero external dependencies; MySQL is already available; sufficient for this app's volume |
| Worker invocation | `php artisan queue:work` | Standard Laravel approach; documented in README or AGENTS.md |
| Test strategy | Fake `Queue` facade + fake `CvAnalysisAgent` | Queue fakes assert dispatch; agent fakes let us control AI response without real API calls |

## Risks / Trade-offs

| Risk | Mitigation |
|------|------------|
| Worker not running in production | Document as a deployment prerequisite; add to AGENTS.md |
| `$backoff` causes long delays between retries | Default to 30s base with exponential backoff; acceptable for async AI analysis |
| Job picks up stale data if candidate deleted before processing | Jobs query models at runtime (`findOrFail`), will fail gracefully |
| `database` queue is slower than Redis for high throughput | Acceptable; not a high-traffic system. Easy to swap driver later |
