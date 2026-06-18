## Why

The config.yaml mandates Eloquent casts for arrays and recommendation enums, and the domain requires typed accessors so that views, controllers, and agents interact with safe typed data instead of raw DB values. Currently, casts exist on CandidateAnalysis and JobOffer but are not formally spec'd, and no typed accessor methods exist for computed values like score labels, skill counts, or recommendation booleans.

## What Changes

- Add typed accessor methods to CandidateAnalysis, JobOffer, and Candidate models
- Add helper methods to the Recommendation enum (`toSelectArray()`, `fromLabel()`)
- Ensure all models have documented casts in their PHPDoc block and in specs
- Document the casts contract in the existing analysis-persistence spec

## Capabilities

### New Capabilities
- `typed-accessors`: Typed PHP accessor methods on models for computed and formatted analysis values (score label, recommendation boolean, skill counts)

### Modified Capabilities
- `analysis-persistence`: Add requirements for the `education_level` cast, `status` cast, and typed accessor contract; add the Recommendation enum helper methods as additional requirements

## Impact

- **Modified files**: `app/Models/CandidateAnalysis.php`, `app/Models/JobOffer.php`, `app/Models/Candidate.php`, `app/Enums/Recommendation.php`
- **Test files**: New unit tests for accessors and enum helpers
- **No migrations**: All casts are already supported by existing columns
