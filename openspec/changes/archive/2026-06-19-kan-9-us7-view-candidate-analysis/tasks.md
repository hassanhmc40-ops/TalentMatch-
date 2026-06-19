## 1. Route & Controller

- [x] 1.1 Add nested route `GET /offres/{offre}/analyses/{analyse}` named `analyses.show` in `routes/web.php`
- [x] 1.2 Add `showAnalysis(JobOffer $offre, CandidateAnalysis $analyse)` method to `JobOfferController` with eager loading (`candidate`, `jobOffer`) and authorization via offer ownership
- [x] 1.3 Handle `pending` status: return view with `$status = 'pending'` flag (view handles display)
- [x] 1.4 Handle `failed` status: return view with `$status = 'failed'` flag (view handles display)

## 2. Analysis Detail View

- [x] 2.1 Create `resources/views/candidate-analyses/show.blade.php` with `x-app-layout` layout
- [x] 2.2 Add breadcrumb navigation: "Mes offres" → offer title → candidate name
- [x] 2.3 Add "Compétences extraites" card section listing extracted skills as badges
- [x] 2.4 Add "Profil du candidat" card section with experience years, education level, languages
- [x] 2.5 Add "Score de correspondance" card section with `<x-progress>` color-coded by score level (`danger`/`warning`/`primary`/`success`)
- [x] 2.6 Add score level label ("Excellent"/"Bon"/"Moyen"/"Faible") next to the progress bar
- [x] 2.7 Add "Points forts et lacunes" card section with two-column layout for strengths and gaps
- [x] 2.8 Add "Compétences manquantes" card section listing missing skills as badges
- [x] 2.9 Add "Recommandation" card section with `<x-badge>` color-coded by recommendation type (`success`/`warning`/`danger`) and justification text
- [x] 2.10 Add pending state: show "Analyse en cours..." with loading indicator when `status = 'pending'`
- [x] 2.11 Add failed state: show "Analyse échouée" with resubmit message when `status = 'failed'`

## 3. Navigation Update

- [x] 3.1 Add "Voir l'analyse" link in the offer detail table rows pointing to `analyses.show`
- [x] 3.2 Keep existing "Assistant →" link as secondary action

## 3. Navigation Update

- [ ] 3.1 Add "Voir l'analyse" link in the offer detail table rows pointing to `analyses.show`
- [ ] 3.2 Keep existing "Assistant →" link as secondary action

## 4. Test Coverage

- [x] 4.1 Write test: analysis detail page returns 200 for authorized user
- [x] 4.2 Write test: analysis detail page returns 403 for unauthorized user (different offer owner)
- [x] 4.3 Write test: analysis detail page returns 404 for non-existent analysis
- [x] 4.4 Write test: pending analysis shows "Analyse en cours..." message
- [x] 4.5 Write test: failed analysis shows "Analyse échouée" message
- [x] 4.6 Write test: completed analysis displays score progress bar with correct value
- [x] 4.7 Write test: completed analysis displays recommendation badge with correct variant
- [x] 4.8 Write test: offer detail page includes link to analysis detail for each candidate

## 5. Code Quality & Spec Sync

- [x] 5.1 Run `vendor/bin/pint` for code formatting
- [x] 5.2 Run full test suite: `php artisan test --compact`
- [x] 5.3 Sync delta specs to main `candidate-analysis-view/spec.md`

## 6. Archive & Push

- [x] 6.1 Archive the change
- [x] 6.2 Commit and push to repository
