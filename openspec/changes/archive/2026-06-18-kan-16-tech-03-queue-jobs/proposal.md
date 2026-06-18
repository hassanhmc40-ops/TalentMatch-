## Why

CV analysis uses an external AI API that can take several seconds per request. Blocking the HTTP response while waiting for AI output creates a poor UX and risks timeout errors. The queue system is already partially implemented (AnalyseCvJob dispatched on candidate submission), but needs formalized retry strategy, failure handling, queue connection configuration, and test coverage for the async flow.

## What Changes

- Formalize the `AnalyseCvJob` implementation: AI call, validation, analysis persistence
- Add retry configuration (attempts, delay, backoff) to the job
- Add `failed()` method or use Laravel's failed job handling for logging and status updates
- Ensure the queue connection (`database`) is properly configured
- Write comprehensive queue-related tests (job dispatched, job performs AI analysis, job handles failure, job retries)
- The job already exists; this change refines its internals and adds the retry/failure safety net

## Capabilities

### New Capabilities
- `queue-jobs`: Queue architecture, AnalyseCvJob implementation, retry strategy, failure handling, and queue connection configuration

### Modified Capabilities
- `candidate-submission`: Update job-failure scenarios to reference the new retry/failure architecture
- `analysis-persistence`: No requirement changes (persistence already covered)

## Impact

- **Modified files**: `app/Jobs/AnalyseCvJob.php` (retry/backoff config, AI call logic, failed method), `.env.example` (queue defaults), `config/queue.php` (if database connection not set)
- **Tests**: New/updated tests for job dispatch, job execution (fake AI), job failure, job retries
- **No new models or migrations**
