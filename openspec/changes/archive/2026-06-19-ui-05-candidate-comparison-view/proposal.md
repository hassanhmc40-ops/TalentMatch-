## Why

HR users currently have no way to visually compare two candidates side-by-side for the same job offer. The `CompareCandidates` tool exists only as an AI agent function — users must ask the conversational assistant to compare, which is indirect and lacks a structured, at-a-glance visual comparison.

## What Changes

- Add a dedicated side-by-side comparison view at `GET /offres/{offre}/comparer?candidats[]=X&candidats[]=Y`
- Add candidate selection mechanism on the offer detail page to select two candidates for comparison
- Add a comparison route and controller method in `JobOfferController`
- Reuse the `CompareCandidates` tool logic or add a new scoped query to fetch comparison data for the UI
- Render score comparison with visual progress bars, strengths/gaps lists per candidate, and recommendation badges
- Display score difference prominently

## Capabilities

### New Capabilities
- `candidate-comparison-ui`: Side-by-side comparison view comparing two candidates for the same job offer, including score comparison, strengths/gaps, recommendation display, and selection mechanism from the offer detail page

### Modified Capabilities
- `job-offer-detail`: Add candidate selection (checkboxes) and a "Comparer" button to the candidate table
- `candidate-analysis-view`: No changes needed

## Impact

- **Routes**: New `GET /offres/{offre}/comparer` route
- **Controller**: New `compare()` method on `JobOfferController`
- **Views**: New `resources/views/offres/comparer.blade.php` comparison page
- **Existing**: Offer detail view updated with candidate selection UI
- **No changes to**: AI tools, conversational agent, or existing analysis view
