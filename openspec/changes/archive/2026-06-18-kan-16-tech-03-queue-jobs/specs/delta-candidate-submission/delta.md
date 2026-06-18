## Purpose

This delta updates the `candidate-submission` spec's job-failure scenarios to reference the formalized queue architecture (declarative retry, failed status tracking, database queue driver).

## Changes

### Modify: Queue job failure scenarios

Replace the existing "Queue job failure is handled gracefully" requirement with updated scenarios that align with the queue-jobs capability.

#### Scenario: Job fails validation and analysis shows failed
- **WHEN** the `AnalyseCvJob` receives an AI response that fails structured validation
- **THEN** the job SHALL NOT retry
- **AND** SHALL update CandidateAnalysis status to `failed`
- **AND** SHALL log the validation errors
- **AND** the candidate SHALL be visible in the offer detail with a "Échec de l'analyse" status

#### Scenario: Job exhausts retries and analysis shows failed
- **WHEN** the `AnalyseCvJob` fails due to transient errors (AI timeout, network error) on all 3 attempts
- **THEN** the job SHALL update CandidateAnalysis status to `failed`
- **AND** SHALL be recorded in the `failed_jobs` table
- **AND** the candidate SHALL be visible in the offer detail with a "Échec de l'analyse" status

#### Scenario: Job succeeds mid-retry
- **WHEN** the `AnalyseCvJob` fails on attempt 1 or 2 due to a transient error
- **AND** succeeds on a subsequent attempt
- **THEN** CandidateAnalysis status SHALL be set to `completed`
- **AND** the analysis data SHALL be persisted normally
