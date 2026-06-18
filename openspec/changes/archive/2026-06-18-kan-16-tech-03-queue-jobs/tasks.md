## 1. Queue Configuration

- [x] 1.1 Verify the `jobs` database table migration exists; create it if missing (`php artisan queue:table`, then `php artisan migrate`)
- [x] 1.2 Verify `QUEUE_CONNECTION=database` in `.env`

## 2. AnalyseCvJob — Declarative Retry

- [x] 2.1 Add `public $tries = 3;` property to `AnalyseCvJob`
- [x] 2.2 Add `public $backoff = [30];` property to `AnalyseCvJob`
- [x] 2.3 Replace the manual `if ($this->attempts() < 3)` / `$this->release(30)` in the `catch (\Throwable)` block with just `$this->release($this->backoff[0])` — the `$tries` property handles max attempt enforcement
- [x] 2.4 Status update to `failed` now handled universally by the `failed()` method

## 3. AnalyseCvJob — Failed Method

- [x] 3.1 Add a `public function failed(\Throwable $e): void` method to `AnalyseCvJob` that logs the error and updates CandidateAnalysis status to `failed`

- [x] 4.1 Add queue worker documentation to `AGENTS.md` or `README.md` explaining how to run `php artisan queue:work` and the prerequisite `jobs` table migration

## 5. Tests

- [x] 5.1 Write test: job is dispatched after valid candidate submission (`Queue::fake()` + assertPushed) — already exists in CandidateSubmissionTest
- [x] 5.2 Write test: job loads models and calls AI agent (mock CvAnalysisAgent, assert `prompt()` is called)
- [x] 5.3 Write test: job persists analysis on AI success (mock agent to return valid response, assert CandidateAnalysis created with `completed` status) — already exists
- [x] 5.4 Write test: job handles validation failure (mock agent returns invalid response, assert `failed` status, assert no retry) — already exists
- [x] 5.5 Write test: job has declarative retry properties ($tries = 3, $backoff = [30])
- [x] 5.6 Write test: job failed() method sets analysis status to failed
- [x] 5.7 Run `vendor/bin/pint --format agent` to ensure code style
- [x] 5.8 Run tests to confirm all pass: `php artisan test --compact`
