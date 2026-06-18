## Purpose

The system SHALL analyse CV submissions asynchronously via a queue job so that HR agents are not blocked waiting for AI responses. This capability covers the queue connection configuration, job implementation (AnalyseCvJob), retry strategy, failure handling, and the queue worker invocation.

## Requirements

### Requirement: Queue connection is configured for database driver

The system SHALL use the `database` queue driver by default.

#### Scenario: Default connection is database
- **WHEN** the application boots
- **THEN** `QUEUE_CONNECTION` SHALL default to `database`
- **AND** the `jobs` database table SHALL exist

#### Scenario: Queue config is present
- **WHEN** inspecting `config/queue.php`
- **THEN** the `database` connection SHALL be configured with `retry_after` set to at least 90 seconds

### Requirement: AnalyseCvJob is dispatched after successful submission

When candidate data passes validation and is persisted, the system SHALL dispatch AnalyseCvJob with the candidate ID and job offer ID.

#### Scenario: Job dispatched with IDs
- **WHEN** a valid candidate submission is processed
- **THEN** `AnalyseCvJob::dispatch($candidateId, $jobOfferId)` SHALL be called
- **AND** the job SHALL receive the correct `candidateId` and `jobOfferId` via its constructor

### Requirement: Job loads models and calls AI

The job SHALL load the Candidate and JobOffer from the database, build a French-language prompt from their data, and call the AI via CvAnalysisAgent.

#### Scenario: Job builds prompt and calls AI
- **WHEN** AnalyseCvJob runs
- **THEN** it SHALL load Candidate and JobOffer models by ID
- **AND** SHALL build a prompt containing the CV text, offer title, description, required skills, and min experience
- **AND** SHALL call `CvAnalysisAgent::make()->prompt($prompt)`

### Requirement: Validated AI response is persisted

After the AI returns a structured response, the job SHALL validate and persist the analysis via ValidateStructuredAnalysis and PersistValidatedAnalysis actions.

#### Scenario: Analysis is persisted on success
- **WHEN** the AI response passes validation
- **THEN** `PersistValidatedAnalysis::persist()` SHALL be called with the validated structured data
- **AND** CandidateAnalysis status SHALL be set to `completed`

### Requirement: Job retries on transient errors

The job SHALL retry up to 3 times with a 30-second backoff delay when the AI call fails due to a transient error (timeout, network issue).

#### Scenario: Job retries on transient failure
- **WHEN** the AI call throws a `\Throwable`
- **AND** the attempt count is less than the maximum (3)
- **THEN** the job SHALL be released back to the queue with a delay
- **AND** the analysis status SHALL remain `pending`

#### Scenario: Job fails permanently after exhausting retries
- **WHEN** the AI call fails on the 3rd attempt
- **THEN** the job SHALL update CandidateAnalysis status to `failed`
- **AND** SHALL fail permanently via `$this->fail()`

### Requirement: Validation failure marks analysis as failed immediately

When the AI response fails validation, there is no value in retrying — the job SHALL fail immediately and mark the analysis as failed.

#### Scenario: Validation failure is not retried
- **WHEN** the AI response fails structural validation
- **THEN** the job SHALL log the validation errors
- **AND** SHALL update CandidateAnalysis status to `failed`
- **AND** SHALL fail permanently via `$this->fail()`
- **AND** SHALL NOT retry

### Requirement: Job declares retry properties

The job SHALL use Laravel's declarative retry properties (`$tries` and `$backoff`) for cleaner retry configuration.

#### Scenario: Retry properties are declared
- **WHEN** inspecting the AnalyseCvJob class
- **THEN** `$tries` SHALL be set to 3
- **AND** `$backoff` SHALL be set to `[30]` (or equivalent array)

### Requirement: Queue worker is documented

The system SHALL document how to start the queue worker for processing jobs.

#### Scenario: Worker command is available
- **WHEN** a developer needs to process jobs
- **THEN** the command `php artisan queue:work` SHALL be available
- **AND** the command SHALL be documented in AGENTS.md or README.md

### Requirement: Failed jobs are logged

When a job fails permanently, the system SHALL record it in the `failed_jobs` table via Laravel's built-in failed job handling.

#### Scenario: Failed job appears in failed_jobs table
- **WHEN** AnalyseCvJob fails permanently
- **THEN** an entry SHALL be created in the `failed_jobs` table
- **AND** the entry SHALL contain the job payload and exception details
