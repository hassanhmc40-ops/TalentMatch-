## Why

The existing `CompareCandidates` tool returns raw comparison data (scores, strengths, gaps) but does not synthesize a conclusion — it leaves the HR agent to manually determine which candidate is stronger. This change adds comparison analysis logic that evaluates both candidates across multiple dimensions and produces a structured comparison verdict with a recommended candidate.

## What Changes

- Enhance `CompareCandidates` tool to include a structured comparison analysis with a verdict (which candidate is stronger and why)
- Add cross-candidate skill gap analysis (skills one candidate has that the other lacks)
- Add a comparison score (weighted evaluation) and a "recommended candidate" field to the tool output
- Add acceptance criteria for the comparison logic including edge cases
- Add a dedicated `ComparisonResult` data object or array structure with typed fields
- Add tests for the enhanced comparison logic

## Capabilities

### New Capabilities
- `candidate-comparison-analysis`: Structured comparison analysis logic that evaluates two candidates across skills, experience, education, and recommendation dimensions, producing a weighted verdict

### Modified Capabilities
- `candidate-analysis-tools`: Update `compareCandidates` requirement to return enhanced output including comparison verdict, recommended candidate, and skill gap analysis

## Impact

- **Tool**: `CompareCandidates::handle()` enhanced with multi-dimensional analysis logic
- **Data**: New `ComparisonResult` structure with verdict, recommended_candidate, comparison_score, skill_gaps fields
- **Tests**: New unit tests for comparison logic, edge cases, and verdict accuracy
- **No changes to**: Database schema, migrations, UI views, or routes
