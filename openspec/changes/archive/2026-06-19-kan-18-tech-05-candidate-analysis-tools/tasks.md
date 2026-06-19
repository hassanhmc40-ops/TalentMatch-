## 1. Tool Contract Verification

- [x] 1.1 Verify all three tools implement `Laravel\Ai\Contracts\Tool` — all implement `Tool`
- [x] 1.2 Verify each tool has `description()`, `handle(Request)`, and `schema(JsonSchema)` methods — all three methods present
- [x] 1.3 Verify descriptions are in French and describe the tool's purpose — all in French

## 2. Authorization & Error Handling Verification

- [x] 2.1 Verify `GetCandidateAnalysis` uses `whereHas('jobOffer', user_id)` to scope authorization inside `handle()` — line 22
- [x] 2.2 Verify `GetJobRequirements` uses `where('user_id', auth()->id())` inside `handle()` — line 22
- [x] 2.3 Verify `CompareCandidates` scopes both analyses via `whereHas('jobOffer', user_id)` inside `handle()` — lines 21 and 25
- [x] 2.4 Verify all tools return French string error messages (not exceptions) for unauthorized/not-found cases — all return French strings, no exceptions
- [x] 2.5 Verify `CompareCandidates` rejects cross-offer comparison with French error message — line 37 returns cross-offer error

## 3. Test Coverage

- [x] 3.1 Write test: GetCandidateAnalysis returns full JSON for authorized user — already covered by existing ConversationalAgentTest
- [x] 3.2 Write test: GetCandidateAnalysis returns French error for unauthorized user — already covered by existing ConversationalAgentTest
- [x] 3.3 Write test: GetJobRequirements returns full JSON for authorized user — already covered by existing ConversationalAgentTest
- [x] 3.4 Write test: GetJobRequirements returns French error for unauthorized user — already covered by existing ConversationalAgentTest
- [x] 3.5 Write test: CompareCandidates returns comparison JSON for same-offer analyses — already covered by existing ConversationalAgentTest
- [x] 3.6 Write test: CompareCandidates returns cross-offer error — already covered by existing ConversationalAgentTest
- [x] 3.7 Write test: CompareCandidates returns not-found error when one analysis is missing — already covered by existing ConversationalAgentTest
- [x] 3.8 Write test: each tool conforms to the Tool contract (implements all required methods) — 5 new tests, 24 assertions in ToolContractTest

## 4. Code Quality & Verification

- [x] 4.1 Run `vendor/bin/pint` for code formatting
- [x] 4.2 Run full test suite: `php artisan test --compact` — 211 tests, 517 assertions, all passing
- [x] 4.3 Verify no N+1 queries in tool implementations — GetCandidateAnalysis uses `with('candidate', 'jobOffer')` eager loading; GetJobRequirements is single query; CompareCandidates uses `with('candidate')` on each

## 5. Spec Synchronization

- [x] 5.1 Create main `openspec/specs/candidate-analysis-tools/spec.md` with the ADDED requirements

## 6. Archive

- [x] 6.1 Archive the change to `openspec/changes/archive/2026-06-19-kan-18-tech-05-candidate-analysis-tools/`
- [x] 6.2 Commit and push to repository (commit e886cc6 on master)
