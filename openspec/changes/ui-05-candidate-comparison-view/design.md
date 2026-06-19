## Context

The `CompareCandidates` AI tool already exists and returns comparison JSON for the conversational agent, but there is no dedicated web UI for side-by-side comparison. The offer detail page lists candidates in a table with individual actions (view analysis, assistant), but no way to select and compare two candidates visually.

## Goals / Non-Goals

**Goals:**
- Allow HR users to select two candidates from the offer detail candidate table and view a side-by-side comparison
- Display score visualization, strengths/gaps lists, and recommendation badges for each candidate
- Show score difference prominently between the two candidates
- Reuse existing Blade components (`<x-recommendation-badge>`, `<x-progress-bar>`) for visual consistency

**Non-Goals:**
- Comparing more than two candidates at once (out of scope)
- AI-driven comparison descriptions — the UI is data-driven from the database
- Modifying the existing `CompareCandidates` AI tool

## Decisions

- **Route pattern**: `GET /offres/{offre}/comparer` with query params `?candidats[]=X&candidats[]=Y` — uses existing route model binding for the offer; candidate IDs passed as query params for clean URL sharing
- **Data retrieval**: A new scoped query in `JobOfferController::compare()` using eager-loaded `CandidateAnalysis` for the two selected candidates, scoped to the authenticated user's offer — avoids coupling to the AI tool's auth context
- **Candidate selection**: Add checkboxes to the candidate table on `offres.show`, with a "Comparer les candidats sélectionnés" button disabled unless exactly 2 are selected — uses simple Alpine.js state for selection tracking
- **UI layout**: Two equal-width columns on large screens, stacked on mobile, with a persistent score difference banner at the top — Tailwind CSS grid layout
- **Score visualization**: Reuse the existing `<x-progress>` component with color coding per score level, showing both the progress bar and the numeric value

## Risks / Trade-offs

- [Risk] User selects candidates from different offers via URL manipulation → Mitigated by gate check and scoped queries: the route binds the offer, and candidates are queried only within that offer
- [Risk] Candidate analysis is pending or failed → Mitigated by checking analysis status and showing appropriate status message instead of comparison data
- [Risk] Only one candidate has a completed analysis → Mitigated by showing "Analyse non disponible" for candidates without completed analysis
