## 1. Model Scope

- [x] 1.1 Add `latestMessage` HasOne relationship to `AgentConversation` (ordered by `created_at DESC`)
- [x] 1.2 Add `scopeWithCandidateAnalysis()` scope to eager-load `candidateAnalysis.candidate` and `candidateAnalysis.jobOffer`
- [x] 1.3 Add `scopeForDashboard()` scope combining user filter, candidate analysis existence, and ordering by `updated_at DESC`

## 2. Dashboard Controller

- [x] 2.1 Update `DashboardController` to load 10 most recent conversations with eager-loaded relationships
- [x] 2.2 Pass conversations data to the dashboard view

## 3. Dashboard View

- [x] 3.1 Add "Conversations récentes" section card below the analyses table
- [x] 3.2 Implement conversation table with columns: Candidat, Offre, Titre, Messages, Dernier message, Activité
- [x] 3.3 Add Alpine.js search input that filters by candidate name and offer title
- [x] 3.4 Add Alpine.js date range filter (from/to inputs) that filters by `updated_at`
- [x] 3.5 Add empty state when no conversations exist
- [x] 3.6 Wire each conversation row to `route('conversations.show', [$analysis->jobOffer, $analysis->candidate])`

## 4. Test Coverage

- [x] 4.1 Write test: dashboard displays conversations section for user with conversations
- [x] 4.2 Write test: dashboard hides conversations section or shows empty state for user without conversations
- [x] 4.3 Write test: conversations without `candidate_analysis_id` are excluded
- [x] 4.4 Write test: only authenticated user's conversations are shown
- [x] 4.5 Write test: conversation shows candidate name, offer title, message count

## 5. Code Quality & Archive

- [ ] 5.1 Run `vendor/bin/pint` for code formatting
- [ ] 5.2 Run full test suite: `php artisan test --compact`
- [ ] 5.3 Sync delta spec to main spec
- [ ] 5.4 Archive the change
- [ ] 5.5 Commit and push to repository
