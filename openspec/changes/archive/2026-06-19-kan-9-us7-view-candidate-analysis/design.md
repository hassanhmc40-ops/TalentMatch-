## Context

Currently, HR users can only see analysis results as a single score/recommendation per row in the offer detail table, or by asking the AI assistant in the conversation view. There is no standalone analysis detail report. The `CandidateAnalysis` model already stores 10 structured fields (skills, experience, education, languages, score, strengths, gaps, missing skills, recommendation, justification). The design system already has `progress`, `badge`, and `card` components with semantic color variants (`success`, `warning`, `danger`, `neutral`) that map naturally to score levels and recommendation types.

## Goals / Non-Goals

**Goals:**
- Provide a dedicated, scannable analysis detail page at `GET /offres/{offre}/analyses/{analyse}`
- Display all analysis fields in a structured report layout using existing components
- Visualize `matching_score` with `<x-progress>` (color-coded by score level)
- Display `recommendation` with `<x-badge>` (variant color per recommendation type)
- Add navigation from the offer detail page candidate table to the analysis page
- Add breadcrumb navigation back to the offer
- Authorization via job offer ownership (existing pattern)

**Non-Goals:**
- No new database columns or migrations (all data already exists)
- No AI changes (prompts, tools, agent behavior unaffected)
- No changes to the conversation flow
- No candidate comparison on this page (existing compare tool is AI-only)

## Decisions

1. **RESTful nested route** — `GET /offres/{offre}/analyses/{analyse}` with route name `analyses.show`. Nesting under `offres` enforces the existing authorization pattern: the user must own the parent offer. Using implicit route model binding for both `{offre}` and `{analyse}` ensures 404 for unauthorized access without manual policy checks.

2. **No new controller** — Add a `show()` method to the existing `JobOfferController` (already handles analyses via `candidateAnalyses`). This keeps the analysis view close to the offer context and avoids route proliferation. Alternative (dedicated `CandidateAnalysisController`) was rejected because it adds ceremony without benefit for a single read-only action.

3. **Report layout using existing components** — Use `<x-card>` sections for grouping: "Compétences extraites", "Profil du candidat" (experience/education/languages), "Score de correspondance" (progress bar + score level label), "Points forts et lacunes" (two-column layout), and "Recommandation" (badge + justification). No new components needed.

4. **Score progress bar color** — Map score level to progress variant: `danger` (0-30 Faible), `warning` (31-60 Moyen), `primary` (61-80 Bon), `success` (81-100 Excellent). Reuses the existing `scoreLevel()` accessor.

5. **Recommendation badge color** — Map recommendation to badge variant: `success` for Convoquer, `warning` for Attente, `danger` for Rejeter. Uses the existing `Recommendation::label()` for display text.

6. **Navigation from offer detail** — Replace the "Assistant →" action link with a "Voir l'analyse" link to the new route, and keep the assistant link as a secondary action.

## Risks / Trade-offs

- [N+1 on offer detail page] → Eager load analysis + candidate + offer in the controller `show()` method. The offer detail page already loads `candidateAnalyses.candidate`.
- [Score display without context] → Always show the score level label ("Excellent", "Bon", etc.) next to the progress bar so users understand the band without memorizing thresholds.
