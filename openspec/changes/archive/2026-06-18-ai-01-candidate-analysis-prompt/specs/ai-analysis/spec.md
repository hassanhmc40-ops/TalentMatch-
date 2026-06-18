## MODIFIED Requirements

### Requirement: Prompt is built from candidate CV and job offer data

The `AnalyseCvJob` SHALL build a French-language prompt containing the candidate's CV text and the job offer's title, description, required skills, and minimum experience. The prompt SHALL also include few-shot examples demonstrating the expected output format.

#### Scenario: Prompt contains CV text and offer details
- **WHEN** `AnalyseCvJob::handle()` runs
- **THEN** the prompt passed to `CvAnalysisAgent::prompt()` SHALL contain:
  - The candidate's `cv_text`
  - The job offer title
  - The job offer description
  - The job offer required skills
  - The job offer minimum experience years
  - At least one few-shot example with expected JSON output

#### Scenario: Few-shot examples show complete input-to-output transformation
- **WHEN** inspecting the built prompt
- **THEN** each few-shot example SHALL include a sample CV, a sample job offer, and the complete expected JSON output matching the schema
