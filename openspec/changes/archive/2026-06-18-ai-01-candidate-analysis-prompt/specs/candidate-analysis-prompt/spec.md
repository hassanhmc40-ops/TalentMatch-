## ADDED Requirements

### Requirement: System prompt defines role, task, and output rules

The `CvAnalysisAgent::instructions()` SHALL return a structured multi-section system prompt in French with explicit role, task description, field-level rules, and output constraints.

#### Scenario: System prompt has clearly separated sections
- **WHEN** inspecting `CvAnalysisAgent::instructions()`
- **THEN** the prompt SHALL contain the following sections separated by blank lines:
  - Role definition ("Tu es un assistant RH spécialisé dans l'analyse de CV")
  - Task description ("Analyse le CV du candidat et compare-le à l'offre d'emploi")
  - Field-by-field output rules
  - Score calculation logic
  - Anti-hallucination guardrail
  - Recommendation criteria

### Requirement: Score calculation uses weighted reasoning

The prompt SHALL instruct the AI to calculate `matching_score` using a reasoning chain based on skills match, experience adequacy, language fit, and education relevance — not a hardcoded formula.

#### Scenario: Score logic is described as reasoning criteria
- **WHEN** inspecting `CvAnalysisAgent::instructions()`
- **THEN** the score calculation section SHALL mention the following criteria:
  - Skills match (required vs possessed)
  - Years of experience adequacy
  - Language proficiency match
  - Education level relevance
- **AND** SHALL NOT contain a hardcoded arithmetic formula

### Requirement: Anti-hallucination guardrails prevent invented data

The prompt SHALL include explicit instructions telling the AI not to invent skills, experience, languages, or any data not present in the CV text.

#### Scenario: Guardrail clauses are present for each field category
- **WHEN** inspecting `CvAnalysisAgent::instructions()`
- **THEN** the prompt SHALL contain at least one "ne pas inventer" or equivalent anti-hallucination clause
- **AND** SHALL instruct the model to use "Aucun" or an empty list for missing data rather than fabricating values

#### Scenario: Missing data handling is explicit
- **WHEN** the CV text does not specify a candidate's education level
- **THEN** `niveau_etudes` SHALL be "Non spécifié"
- **WHEN** the CV text does not list any languages
- **THEN** `langues` SHALL be an empty array
- **WHEN** the CV text does not list skills relevant to the offer
- **THEN** `competences_extraites` SHALL be an empty array

### Requirement: Few-shot examples demonstrate expected output

The user prompt built by `AnalyseCvJob` SHALL include 1-2 complete worked examples showing a CV + job offer → structured JSON output, to demonstrate the expected scoring and recommendation logic.

#### Scenario: Few-shot examples are appended to the prompt
- **WHEN** inspecting the prompt built by `AnalyseCvJob`
- **THEN** it SHALL contain at least one complete example with:
  - A sample CV text
  - A sample job offer (title, description, skills, min experience)
  - The expected JSON output matching the schema
