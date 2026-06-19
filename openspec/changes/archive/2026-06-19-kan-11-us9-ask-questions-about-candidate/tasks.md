## 1. Agent Context Injection

- [x] 1.1 Add `$context` property and `setContext(array $context)` method to `ConversationalAgent`
- [x] 1.2 Update `instructions()` to prepend a context block when `$context` is set: candidate name, offer title, analysis ID, matching score, recommendation, key skills, and all IDs (candidat, offre, analyse)
- [x] 1.3 Add answer formatting rules for score, skills, recommendation, and comparison questions
- [x] 1.4 Add follow-up question handling rule (use previous context, no need to repeat candidate name)
- [x] 1.5 Add out-of-scope handling rule (politely decline non-analysis questions)

## 2. Controller Wiring

- [x] 2.1 Update `ConversationController::store()` — after loading the analysis, pass context to the agent via `setContext()` before calling `prompt()`
- [x] 2.2 Update `ConversationController::stream()` — same context injection before `stream()`

## 3. Test Coverage

- [x] 3.1 Write test: agent with context can answer score question without tool call
- [x] 3.2 Write test: agent with context formats answers per question type rules
- [x] 3.3 Write test: agent handles out-of-scope questions politely
- [x] 3.4 Write test: follow-up question uses previously established context

## 4. Code Quality & Archive

- [ ] 4.1 Run `vendor/bin/pint` for code formatting
- [ ] 4.2 Run full test suite: `php artisan test --compact`
- [ ] 4.3 Archive the change
- [ ] 4.4 Commit and push to repository
